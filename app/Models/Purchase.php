<?php

namespace App\Models;

use App\Events\PurchaseUpdated;
use App\Events\SalePurchaseUpdated;
use App\Events\SaleUpdated;
use App\Events\UserBalanceUpdated;
use App\Jobs\ProcessTrade;
use App\Mail\PurchaseCancelled;
use App\Mail\PurchaseConfirmed;
use App\Mail\PurchaseDealt;
use App\Mail\PurchasePaid;
use App\Mail\PurchaseRejected;
use App\Mail\SalePurchased;
use App\Utility\TronWeb;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Purchase extends Model
{
    use HasFactory;

    protected $guarded = ['id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tradeFees()
    {
        return $this->hasMany(TradeFee::class);
    }

    public function networkTransaction()
    {
        return $this->morphOne(NetworkTransaction::class, 'recordable');
    }

    public function token()
    {
        return $this->belongsTo(Token::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function transferToken()
    {
        $network = $this->sale->network();
        if ($network->name == 'tron') {
            $transactionId = TronWeb::transferFrom(
                $this->sale->user->wallet($network),
                $this->user->wallet($network),
                $this->amount
            );
            if (is_string($transactionId)) {
                $this->transaction_id = $transactionId;
                $this->save();
                $network->setBalance();
                return $this;
            }
        }
    }

    public function processDealtTransaction()
    {
        if ($this->dealt_at && in_array($this->status, [3, 8]) && !$this->transaction_id) {
            ProcessTrade::dispatch($this);
        }
    }

    public function paid($salePayment)
    {

        // $this->account = $payment->details->account;
        // $this->bank_name = $payment->details->bank_name;
        // $this->bank_branch = $payment->details->bank_branch;
        // $this->bank_username = $payment->details->bank_username;
        $this->payment_id = $salePayment->payment_id;
        $this->sale_account = $salePayment->account;
        $this->sale_bank_name = $salePayment->bank_name;
        $this->sale_bank_branch = $salePayment->bank_branch;
        $this->sale_bank_username = $salePayment->bank_username;
        $this->sale_mobile = $salePayment->mobile;

        $this->paid_at = now();
        $this->status = 2;
        return $this->save();
    }

    public function getOrderNumberAttribute()
    {
        return "#" . $this->created_at->timestamp . ($this->id * 6);
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['order_number'];

    // public function getSellerPaymentInfoAttribute()
    // {
    //     return !$this->paid_at ? null : $this->sale->user->payments()->wherePivot('payment_id', '=', $this->payment_id)->first();
    // }

    // public function getBuyerPaymentInfoAttribute()
    // {
    //     return !$this->paid_at ? null : $this->user->payments()->wherePivot('payment_id', '=', $this->payment_id)->first();
    // }

    public function scopeFilter($query, array $filters)
    {
        $query->when(
            $filters['status'] ?? false,
            function ($query, $status) {
                return $query->where('status', '=', $status);
            }
        );

        $query->when(
            $filters['sale_id'] ?? false,
            function ($query, $saleId) {
                return $query->where('sale_id', '=', $saleId);
            }
        );

        $query->when(
            $filters['sort'] ?? false,
            function ($query, $sort) {
                return $query->orderBy('created_at', $sort);
            }
        );

        $query->when(
            $filters['id'] ?? false,
            function ($query, $id) {
                return $query->where('id', '=', $id);
            }
        );

        $query->when(
            $filters['buyer_email'] ?? false,
            function ($query, $buyer_email) {
                $buyer = User::where('email', $buyer_email)->first();
                return $query->where('user_id', '=', $buyer ? $buyer->id : 0);
            }
        );

        $query->when(
            $filters['seller_email'] ?? false,
            function ($query, $seller_email) {
                $seller = User::where('email', $seller_email)->first();
                $saleIds = Sale::where('user_id', $seller->id)->pluck('id');
                return $query->whereIn('sale_id', $saleIds);
            }
        );
    }

    public function dealt()
    {
        $this->dealt_at = now();
        $this->status = 3;
        return $this->save();
    }

    public function reviewed($status)
    {
        $this->status = $status;
        if ($status == 8) {
            $this->dealt_at = now();
        } else if ($status == 9) {
            SaleUpdated::dispatch($this->sale);
        }
        return $this->save();
    }

    public function sendPurchaseCancelledNotification()
    {
        SalePurchaseUpdated::dispatch($this);
        SaleUpdated::dispatch($this->sale);
        Mail::to($this->sale->user)->queue(new PurchaseCancelled($this));
        Log::channel('emails')->info("Email 'Purchase Cancelled' is queue to send to " . $this->sale->user->email);
    }

    public function sendPurchasePaidNotification()
    {
        SalePurchaseUpdated::dispatch($this);
        Mail::to($this->sale->user)->queue(new PurchasePaid($this));
        Log::channel('emails')->info("Email 'Purchase Paid' is queue to send to " . $this->sale->user->email);
    }

    public function sendPurchaseDealtNotification()
    {
        PurchaseUpdated::dispatch($this);
        Mail::to($this->user)->queue(new PurchaseDealt($this));
        Log::channel('emails')->info("Email 'Purchase Dealt' is queue to send to " . $this->user->email);
    }

    public function sendPurchaseRejectedNotification()
    {
        PurchaseUpdated::dispatch($this);
        Mail::to($this->user)->queue(new PurchaseRejected($this));
        Log::channel('emails')->info("Email 'Purchase Rejected' is queue to send to " . $this->user->email);
    }

    public function sendPurchaseCreatedNotification()
    {
        Mail::to($this->user)->queue(new SalePurchased($this));
        Log::channel('emails')->info("Email 'Sale Purchased' is queue to send to " . $this->user->email);
    }

    public function sendPurchaseConfirmedNotification()
    {
        PurchaseUpdated::dispatch($this);
        SaleUpdated::dispatch($this->sale);
        $network = $this->sale->network();
        UserBalanceUpdated::dispatch($this->user->wallet($network)->balance($this->sale->token), $this->sale->token->id, $this->user->id);
        UserBalanceUpdated::dispatch($this->sale->user->wallet($network)->balance($this->sale->token), $this->sale->token->id, $this->sale->user->id);

        Mail::to($this->user)->queue(new PurchaseConfirmed($this));
        Log::channel('emails')->info("Email 'Purchase Confirmed' is queue to send to " . $this->user->email);
    }
}
