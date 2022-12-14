<?php

namespace App\Constants;

class Endpoint
{
    const GET_USD_RATE = '/get-usd-rate';
    const AGENT_USER_WITHDRAW = '/agent-user-withdraw';
    const AGENT_GET_USER_WALLETS = '/agent-get-user-wallets';
    const AGENT_GET_TOKEN_SUMMERY = '/agent-get-token-summery';
    const AGENT_GET_NETWORK_TRANSACTIONS = '/agent-get-network-transations';
    const AGENT_CREATE_USER = '/agent-create-user';
    const LIST_NETWORK_TRANSACTIONS = '/network-transaction';
    const RESET_2FA = '/reset-2fa';
    const GET_TOKEN_SUMMERY = '/token-summery';
    const BAN = '/ban';
    const CREATE_PAYMENT = '/payment';
    const USABLE_PAYMENTS = '/usable-payments';
    const REGISTER = '/register';
    const LOGIN = '/login';
    const LOGOUT = '/logout';
    const VERIFY_EMAIL_OTP = '/verify-email';
    const VERIFY_EMAIL_LINK = '/verify-email-link';
    const REQUEST_OTP = '/request-otp';
    const RESET_PASSWORD = '/reset-password';
    const CHANGE_PASSWORD = '/change-password';
    const FORGOT_PASSWORD = '/forgot-password';
    const VERIFY_FORGOT_PASSWORD = '/verify-forgot-password';
    const GOOGLE_2FA = '/google-2fa';
    const VERIFY_GOOGLE_2FA = '/verify-google-2fa';
    const GET_OTP_ABILITY = '/otp-ability';
    const EMAIL_VERIFICATION_LINK = '/email-verification-link';
    const PROPOSE_VERIFY_USER = '/propose-verify-user';
    const GET_IDENTIFIERS = '/identifier';
    const GET_NETWORK_ACCOUNT_INFO = '/network-account-info';
    const GET_ALL_SALE = '/all-sale';
    const GET_SALE = '/sale';
    const CREATE_SALE = '/sale';
    const UPDATE_SALE = '/sale';
    const CREATE_PURCHASE = '/purchase';
    const PAYMENT = '/payment';
    const PURCHASE_PAID = '/purchase-paid';
    const PURCHASE_DEALT = '/purchase-dealt';
    const GET_GOOGLE_FORM_FIELDS = '/goo-form-fields';
    const GET_GOOGLE_FORM_FIELDS_QR = '/goo-form-fields-qr';
    const ME = '/me';
    const GET_WALLET_TOKEN = '/get-wallet-token';
    const GET_TOKEN = '/token';
    const GET_CURRENCY = '/currency';
    const GET_SELF_SALE = '/self-sale';
    const CANCEL_PURCHASE = '/cancel-purchase';
    const REJECT_PURCHASE = '/reject-purchase';
    const REJECT_IDENTIFIER = '/reject-identifier';
    const CONFIRM_IDENTIFIER = '/confirm-identifier';
    const TRON_EVENT  = '/tron-event';
    const GET_CRYPTO_NETWORK  = '/crypto_network';
    const HANDSHAKE  = '/handshake';
    const SYNC_WALLET = '/sync-wallet';
    const SET_SETTING = '/setting';
    const UPDATE_SETTING = '/setting';
    const ADD_PAYMENT = '/add-payment';
    const UPDATE_PAYMENT = '/update-payment';
    const WITHDRAW = '/withdraw';
    const GET_A_PURCHASE = '/purchase';
    const REVIEW_PURCHASE = '/review-purchase';
    const PURCHASE_REVIEWED = '/purchase-reviewed';
    const REMOVE_PAYMENT = '/remove-payment';
    const GET_WALLETS = '/get-wallets';
    const GET_PURCHASES = '/get-purchases';
    const CREATE_NETWORK = '/crypto-network';
    const CREATE_TOKEN = '/token';
    const CREATE_CURRENCY = '/currency';
    const CREATE_IDENTIFIER = '/identifier';
    const GET_USER_IDENTITIES = '/user-identities';
    const GET_USERS = '/user';
    const UNBAN = '/unban';
    const LIST_PURCHASES = '/purchase';
    const GET_SALE_PURCHASES = '/sale-purchases';
    const CREATE_AGENT = '/agent';
    const LIST_AGENT = '/agent';
    const TOGGLE_BLOCK_AGENT = '/toggle-block-agent';
    const RESET_AGENT_KEY = '/reset-agent-key';
}
