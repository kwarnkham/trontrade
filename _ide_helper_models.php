<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\Agent
 *
 * @property int $id
 * @property string $name
 * @property mixed $key
 * @property string|null $remark
 * @property int $status
 * @property string $ip
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\AgentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Agent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Agent query()
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereUpdatedAt($value)
 */
	class Agent extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Approval
 *
 * @property int $id
 * @property string|null $transaction_id
 * @property int $crypto_wallet_id
 * @property string $spender
 * @property int $token_id
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CryptoWallet $cryptoWallet
 * @property-read mixed $from
 * @property-read mixed $to
 * @property-read \App\Models\NetworkTransaction|null $networkTransaction
 * @property-read \App\Models\Token $token
 * @method static \Database\Factories\ApprovalFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval query()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereCryptoWalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereSpender($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereUpdatedAt($value)
 */
	class Approval extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CryptoNetwork
 *
 * @property int $id
 * @property string $name
 * @property string|null $icon
 * @property string $api_url
 * @property mixed|null $api_key
 * @property float $trade_fees
 * @property float $withdraw_fees
 * @property string|null $address
 * @property float $balance
 * @property mixed|null $resources
 * @property mixed|null $private_key
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CryptoWallet[] $cryptoWallets
 * @property-read int|null $crypto_wallets_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Token[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\CryptoNetworkFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork query()
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork whereApiUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork wherePrivateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork whereResources($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork whereTradeFees($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoNetwork whereWithdrawFees($value)
 */
	class CryptoNetwork extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CryptoWallet
 *
 * @property int $id
 * @property mixed $private_key
 * @property string|null $public_key
 * @property string|null $base58_check
 * @property string|null $hex_address
 * @property string|null $base64
 * @property int $crypto_network_id
 * @property int $user_id
 * @property string|null $activated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CryptoNetwork $cryptoNetwork
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Token[] $tokens
 * @property-read int|null $tokens_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WalletActivation[] $walletActivations
 * @property-read int|null $wallet_activations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Withdraw[] $withdraws
 * @property-read int|null $withdraws_count
 * @method static \Database\Factories\CryptoWalletFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet query()
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet whereActivatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet whereBase58Check($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet whereBase64($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet whereCryptoNetworkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet whereHexAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet wherePrivateKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet wherePublicKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CryptoWallet whereUserId($value)
 */
	class CryptoWallet extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Currency
 *
 * @property int $id
 * @property string $name
 * @property string $symbol
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $Payment
 * @property-read int|null $payment_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sale[] $sales
 * @property-read int|null $sales_count
 * @method static \Database\Factories\CurrencyFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency query()
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Currency whereSymbol($value)
 */
	class Currency extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Deposit
 *
 * @property int $id
 * @property int $crypto_wallet_id
 * @property int $token_id
 * @property float $amount
 * @property mixed $transaction
 * @property string $from
 * @property string $to
 * @property string $transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CryptoWallet $cryptoWallet
 * @property-read \App\Models\NetworkTransaction|null $networkTransaction
 * @property-read \App\Models\Token $token
 * @method static \Database\Factories\DepositFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit query()
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereCryptoWalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereTransaction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Deposit whereUpdatedAt($value)
 */
	class Deposit extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Identifier
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\IdentifierFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Identifier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Identifier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Identifier query()
 * @method static \Illuminate\Database\Eloquent\Builder|Identifier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Identifier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Identifier whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Identifier whereUpdatedAt($value)
 */
	class Identifier extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\NetworkTransaction
 *
 * @property int $id
 * @property string $transaction_id
 * @property int $block_number
 * @property int $block_timestamp
 * @property string $type
 * @property string $from
 * @property string $to
 * @property int $token_id
 * @property float $amount
 * @property float $fees
 * @property mixed $receipt
 * @property string|null $contract_result
 * @property string $status
 * @property int $crypto_network_id
 * @property int|null $recordable_id
 * @property string|null $recordable_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CryptoNetwork $cryptoNetwork
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $recordable
 * @property-read \App\Models\Token $token
 * @method static \Database\Factories\NetworkTransactionFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereBlockNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereBlockTimestamp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereContractResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereCryptoNetworkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereFees($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereReceipt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereRecordableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereRecordableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|NetworkTransaction whereUpdatedAt($value)
 */
	class NetworkTransaction extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OneTimePassword
 *
 * @property int $id
 * @property string $password
 * @property string|null $used_at
 * @property \Illuminate\Support\Carbon|null $expired_at
 * @property int $user_id
 * @property int $otp_ability_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OtpAbility $ability
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\OneTimePasswordFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|OneTimePassword newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OneTimePassword newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OneTimePassword query()
 * @method static \Illuminate\Database\Eloquent\Builder|OneTimePassword whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OneTimePassword whereExpiredAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OneTimePassword whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OneTimePassword whereOtpAbilityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OneTimePassword wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OneTimePassword whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OneTimePassword whereUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OneTimePassword whereUserId($value)
 */
	class OneTimePassword extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\OtpAbility
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OneTimePassword[] $OneTimePasswords
 * @property-read int|null $one_time_passwords_count
 * @method static \Database\Factories\OtpAbilityFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|OtpAbility newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OtpAbility newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OtpAbility query()
 * @method static \Illuminate\Database\Eloquent\Builder|OtpAbility whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtpAbility whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtpAbility whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OtpAbility whereUpdatedAt($value)
 */
	class OtpAbility extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\PasswordChange
 *
 * @property int $id
 * @property string $type
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange query()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordChange whereUserId($value)
 */
	class PasswordChange extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Payment
 *
 * @property int $id
 * @property string $name
 * @property string $chinese_name
 * @property int $currency_id
 * @property string $icon
 * @property string $country
 * @property string $color
 * @property int $enabled
 * @property int $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Currency $currency
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\PaymentFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereChineseName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Payment whereUpdatedAt($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Purchase
 *
 * @property int $id
 * @property int $user_id
 * @property int $sale_id
 * @property int $token_id
 * @property int|null $payment_id
 * @property string $from
 * @property string $to
 * @property float $amount
 * @property float $unit_price
 * @property string|null $transaction_id
 * @property int $status
 * @property string|null $paid_at
 * @property string|null $dealt_at
 * @property string|null $confirmed_at
 * @property string|null $account
 * @property string|null $bank_name
 * @property string|null $bank_username
 * @property string|null $bank_branch
 * @property string|null $mobile
 * @property string|null $sale_account
 * @property string|null $sale_bank_name
 * @property string|null $sale_bank_username
 * @property string|null $sale_bank_branch
 * @property string|null $sale_mobile
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $order_number
 * @property-read \App\Models\NetworkTransaction|null $networkTransaction
 * @property-read \App\Models\Payment|null $payment
 * @property-read \App\Models\Sale $sale
 * @property-read \App\Models\Token $token
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\TradeFee[] $tradeFees
 * @property-read int|null $trade_fees_count
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\PurchaseFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase query()
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereBankBranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereBankUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereDealtAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase wherePaidAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase wherePaymentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereSaleAccount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereSaleBankBranch($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereSaleBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereSaleBankUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereSaleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereSaleMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Purchase whereUserId($value)
 */
	class Purchase extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\RetriedTrade
 *
 * @property int $id
 * @property int $purchase_id
 * @property string|null $confirmed_at
 * @property string|null $transaction_id
 * @property string|null $trade_fees_transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\RetriedTradeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|RetriedTrade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RetriedTrade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RetriedTrade query()
 * @method static \Illuminate\Database\Eloquent\Builder|RetriedTrade whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RetriedTrade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RetriedTrade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RetriedTrade wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RetriedTrade whereTradeFeesTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RetriedTrade whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RetriedTrade whereUpdatedAt($value)
 */
	class RetriedTrade extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Role
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\RoleFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Role whereUpdatedAt($value)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Sale
 *
 * @property int $id
 * @property float $amount
 * @property float $min
 * @property float $max
 * @property float $price
 * @property int $token_id
 * @property int $user_id
 * @property int $currency_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Currency $currency
 * @property-read mixed $paid_purchased_amount
 * @property-read mixed $payments
 * @property-read mixed $sold_out
 * @property-read mixed $trading
 * @property-read mixed $unpaid_purchased_amount
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Purchase[] $purchases
 * @property-read int|null $purchases_count
 * @property-read \App\Models\Token $token
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\SaleFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale query()
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereMax($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereMin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Sale whereUserId($value)
 */
	class Sale extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Setting
 *
 * @property int $id
 * @property string|null $hidden_tokens
 * @property string|null $locale
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $User
 * @method static \Database\Factories\SettingFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting query()
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereHiddenTokens($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Setting whereUserId($value)
 */
	class Setting extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Token
 *
 * @property int $id
 * @property string $name
 * @property string $display_name
 * @property string $unit
 * @property string|null $address
 * @property int|null $decimals
 * @property int $crypto_network_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CryptoNetwork $cryptoNetwork
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sale[] $sales
 * @property-read int|null $sales_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CryptoWallet[] $wallet
 * @property-read int|null $wallet_count
 * @method static \Database\Factories\TokenFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Token newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Token newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Token query()
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereCryptoNetworkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereDecimals($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereUnit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Token whereUpdatedAt($value)
 */
	class Token extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\TradeFee
 *
 * @property int $id
 * @property int $purchase_id
 * @property int $token_id
 * @property string $transaction_id
 * @property string $from
 * @property string $to
 * @property float $amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\NetworkTransaction|null $networkTransaction
 * @property-read \App\Models\Purchase $purchase
 * @property-read \App\Models\Token $token
 * @method static \Database\Factories\TradeFeeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeFee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TradeFee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TradeFee query()
 * @method static \Illuminate\Database\Eloquent\Builder|TradeFee whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeFee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeFee whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeFee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeFee wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeFee whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeFee whereTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeFee whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TradeFee whereUpdatedAt($value)
 */
	class TradeFee extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $google2fa_secret_verified_at
 * @property string|null $ip
 * @property \Illuminate\Support\Carbon|null $banned_at
 * @property string $password
 * @property mixed|null $google2fa_secret
 * @property int|null $referrer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $agent_id
 * @property-read \App\Models\Agent $agent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\CryptoWallet[] $cryptoWallets
 * @property-read int|null $crypto_wallets_count
 * @property-read mixed $average_confirm_time
 * @property-read mixed $identity
 * @property-read mixed $invite_code
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Identifier[] $identifiers
 * @property-read int|null $identifiers_count
 * @property-read \App\Models\PasswordChange|null $latestPasswordChange
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OneTimePassword[] $oneTimePasswords
 * @property-read int|null $one_time_passwords_count
 * @property-read \App\Models\OneTimePassword|null $otp
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PasswordChange[] $passwordChanges
 * @property-read int|null $password_changes_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Payment[] $payments
 * @property-read int|null $payments_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Purchase[] $purchases
 * @property-read int|null $purchases_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $referrees
 * @property-read int|null $referrees_count
 * @property-read User|null $referrer
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Sale[] $sales
 * @property-read int|null $sales_count
 * @property-read \App\Models\Setting|null $setting
 * @property-read \Illuminate\Database\Eloquent\Collection|\Laravel\Sanctum\PersonalAccessToken[] $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User filter(array $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereBannedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGoogle2faSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereGoogle2faSecretVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereReferrerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 */
	class User extends \Eloquent implements \App\Contracts\MustVerifyEmail, \Illuminate\Contracts\Translation\HasLocalePreference {}
}

namespace App\Models{
/**
 * App\Models\WalletActivation
 *
 * @property int $id
 * @property int $crypto_wallet_id
 * @property int $token_id
 * @property mixed $activation_result
 * @property string $from
 * @property string $to
 * @property float $amount
 * @property string $transaction_id
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CryptoWallet $cryptoWallet
 * @property-read \App\Models\NetworkTransaction|null $networkTransaction
 * @property-read \App\Models\Token $token
 * @method static \Database\Factories\WalletActivationFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation query()
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation whereActivationResult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation whereCryptoWalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation whereFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation whereTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WalletActivation whereUpdatedAt($value)
 */
	class WalletActivation extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Withdraw
 *
 * @property int $id
 * @property float $amount
 * @property float $withdraw_fees
 * @property string $wallet_address
 * @property string|null $transaction_id
 * @property int $crypto_wallet_id
 * @property int $token_id
 * @property string|null $confirmed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CryptoWallet $cryptoWallet
 * @property-read mixed $from
 * @property-read mixed $to
 * @property-read \App\Models\NetworkTransaction|null $networkTransaction
 * @property-read \App\Models\Token $token
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\WithdrawFee[] $withdrawFees
 * @property-read int|null $withdraw_fees_count
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw query()
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereCryptoWalletId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereWalletAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Withdraw whereWithdrawFees($value)
 */
	class Withdraw extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\WithdrawFee
 *
 * @property int $id
 * @property int $withdraw_id
 * @property int $token_id
 * @property string|null $transaction_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $amount
 * @property-read mixed $from
 * @property-read mixed $to
 * @property-read \App\Models\NetworkTransaction|null $networkTransaction
 * @property-read \App\Models\Token $token
 * @property-read \App\Models\Withdraw $withdraw
 * @method static \Database\Factories\WithdrawFeeFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawFee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawFee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawFee query()
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawFee whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawFee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawFee whereTokenId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawFee whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawFee whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|WithdrawFee whereWithdrawId($value)
 */
	class WithdrawFee extends \Eloquent {}
}

