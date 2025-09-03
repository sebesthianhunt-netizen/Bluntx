# BLVKDOT API Tests

## What to Cover
- Auth: login, signup, KYC
- Wallet: fund, withdraw, transfer, escrow
- Booking: create, confirm, check-in
- Challenge: create, accept, resolve, dispute

## Sample
```http
POST /auth/login
{
  "phone": "+2348000000000",
  "password": "supersafe"
}
```
Expect: 200 OK, JWT token