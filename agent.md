-   HOST = https://tron.jfweb1.xyz
-   AGENT= Test
-   AGENT_KEY= $2y$10$/o/1azEWh.I4P7qJs6q1MOhiq9Yo8VKiTFkb061I6U8pteDfvRXdi

## Get USD Rate

```
curl --location --request GET 'http://127.0.0.1:8000/api/get-usd-rate' \
--header 'x-agent: dev' \
--header 'x-api-key: $2y$10$.H6amDc3rj1/NtdRaXwF/.1quWh3GnYjvF/AR9gSO7XGnmigIedl.' \
--header 'accept: application/json'
```

## Create User

```
curl --location --request POST 'http://127.0.0.1:8000/api/agent-create-user' \
--header 'x-agent: dev' \
--header 'x-api-key: $2y$10$.H6amDc3rj1/NtdRaXwF/.1quWh3GnYjvF/AR9gSO7XGnmigIedl.' \
--header 'accept: application/json' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'username=user1' \
--data-urlencode 'password=123' \
--data-urlencode 'password_confirmation=123'
```

## Get User Wallets

-   can only use the 'user_id' that is registered via this agent

```
curl --location --request GET 'http://127.0.0.1:8000/api/agent-get-user-wallets?user_id=19' \
--header 'x-agent: dev' \
--header 'x-api-key: $2y$10$ZnnsfO02VpR4PK1ZnZ/AhePARdHWDENmhT4P.T7pFGU.Q.skKtQSW' \
--header 'accept: application/json'
```

## Get Network Transactions

-   type[Deposit|Withdraw]

```
curl --location --request GET 'http://127.0.0.1:8000/api/agent-get-network-transations?type=Deposit&start_time=2022-01-20 00:00:00&end_time=2022-01-26 00:00:00' \
--header 'x-agent: dev' \
--header 'x-api-key: $2y$10$ZnnsfO02VpR4PK1ZnZ/AhePARdHWDENmhT4P.T7pFGU.Q.skKtQSW' \
--header 'accept: application/json'
```

## Get Token Summery

-   There's only one token at the moment and the id is '1'

```
curl --location --request GET 'http://127.0.0.1:8000/api/agent-get-token-summery/1' \
--header 'x-agent: dev' \
--header 'x-api-key: $2y$10$ZnnsfO02VpR4PK1ZnZ/AhePARdHWDENmhT4P.T7pFGU.Q.skKtQSW' \
--header 'accept: application/json'
```

## Agent User withdraw

-   {host}/api/agent-user-withdraw/{token_id}/{user_id}
-   There's only one token at the moment and the id is '1'
-   can only use the 'user_id' that is registered via this agent

```
curl --location --request POST 'http://127.0.0.1:8000/api/agent-user-withdraw/1/19' \
--header 'x-agent: dev' \
--header 'x-api-key: $2y$10$ZnnsfO02VpR4PK1ZnZ/AhePARdHWDENmhT4P.T7pFGU.Q.skKtQSW' \
--header 'accept: application/json' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'amount=3' \
--data-urlencode 'wallet_address=TJLGrLQM7obxsvhkDyNBtQKRfGZg6AjC7j'
```
