<?php

namespace App\Constants;


class ResponseStatus
{
    const OK = 200;
    const CREATED = 201;
    const BAD_REQUEST = 400;
    const UNAUTHENTICATED = 401;
    const PAYMENT_REQUIRED = 402;
    const UNAUTHORIZED = 403;
    const NOT_FOUND = 404;
    const VALIDATION_ERROR = 422;
    const SERVER_ERROR = 500;
    const SERVICE_UNAVAILABLE = 503;
}
