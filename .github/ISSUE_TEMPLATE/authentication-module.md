---
name: Authentication Module
about: Securing access by verifying user identity.
title: Authentication Module
labels: ''
assignees: ''

---

# Authentication Module

An authentication module's task is to verify the identity of users or entities accessing a system or service, ensuring only authorized access.

## Database Structure 

#### 1. Table : `users`

| Field | Datatype | Required |Note |
| --- | --- | --- | ------ |
| id | Int(8) | Yes | Primary Key |
| email | varchar(128) | Yes | Index |
| password | varchar(128) | Yes ||
| firstname | varchar(128) | No ||
| lastname | varchar(128) | No ||
| username | varchar(128) | No | Index |
| country_code | int(8) | No ||
| mobile_number | varchar(32) | No ||
| created_at | timestamp | Yes | Created timestamp |
| updated_at | timestamp | No |  |
| deleted_at | timestamp | No |  |
| created_by | Int(8) | No | |
| updated_by | Int(8) | No |  |
| deleted_by | Int(8) | No |  |

> [!NOTE]  
> The `users` table will include only these specific fields. If you need to store additional user details, you should create a separate **`profile`** table with the relevant fields. This table will incorporate a `user_id` field to establish a one-to-one relationship.

#### 2. Table : `profile`

> [!WARNING] 
> **Please note that the `profile` table is optional,  you should only create it if you intend to include additional user-related fields.**

| Field | Datatype | Required |Note |
| --- | --- | --- | ------ |
| id | Int(8) | Yes | Primary Key |
| user_id | Int(8) | Yes | Foreign Key: `users` |
| created_at | timestamp | Yes | Created timestamp |
| updated_at | timestamp | No |  |
| deleted_at | timestamp | No |  |
| created_by | Int(8) | No | |
| updated_by | Int(8) | No |  |
| deleted_by | Int(8) | No |  |
| ...  | ... | ... | All the other additional user-related fields | 

#### 3. Table : `user_otps`

| Field | Datatype | Required |Note |
| --- | --- | --- | ------ |
| id | Int(8) | Yes | Primary Key |
| user_id | Int(8) | No | Index |
| otp | varchar(32) | Yes ||
| used_for | enum('verification','reset_password') | Yes |Index | 
| verified_at | timestamp | No |  |
| created_at | timestamp | Yes | Created timestamp |
| updated_at | timestamp | No |  |
| deleted_at | timestamp | No |  |

## Role and Permission

By default, the system should have an admin and user role. Kindly use the [Laravel Permission](https://spatie.be/docs/laravel-permission/v5/introduction) package for that. 

## Endpoints

> [!IMPORTANT]
> **For all endpoints that require authentication, kindly ensure that the token is included in the Authorization header of the API request.**

**Base URL : `api/v1/`**

| Endpoint  |  Method | Argument  | Response | Authentication Required | Description |
|---|---|---|---|---|---|
| send-otp | Post |  [Send OTP](#send-otp)  | [Send OTP response](#success-response)  | No |  Send the OTP to the mobile number or email.  |
| verify-otp | Post | [Verify OTP](#verify-otp) | [Verify OTP Response](#success-response)  | No |  Verify the OTP |
| signup | Post | [SignUp Request](#signup)  | [SignUp response](#login-response)  | No | We will require email during the signup process, and to verify it the generated token will be stored in the `user_otps` table. |
| login |  Post |   [Login Request](#login-request)  | [Login response](#login-response)  | No | This endpoint is intended for user login using an **email** . |
| forgot-password | Post |  [Forgot Password Request](#forgot-password) | [Forgot Password Response](#success-response) | No | It requires either a mobile number or an email address to be provided. |
| reset-password |  Post |   [Reset password Request](#reset-password) | [Reset Password Response](#success-response) | No | It requires either a mobile number or an email address to be provided. |
| me |  Get |  |  [User Response](#user-object) | Yes | To retrieve the profile of the logged-in user, please include the token in the Authorization header of the API request. |
| me |  Post | [Update Profile Request](#update-profile) |  [User Response](#user-object) | Yes |  |
| change-password |  Post | [Change Password Request](#change-password) |  [Success Response](#success-response) | Yes |  |

We don't need to create an API to verify the email address. Instead, we will need to generate a web endpoint.


| Endpoint  |  Method | Argument  | Response | Authentication Required | Description |
|---|---|---|---|---|---|
| user/verify/{token} | Get |   | [Success response](#success-response)  | No |  |

### Request Object

1. <span id="send-otp">**Send OTP params**</span>
```yaml
{
    email: String
}
```

2. <span id="verify-otp">**Verify OTP params**</span>
```yaml
{
    email: String
    used_for: String
    otp: String
}
```

3. <span id="signup">**SignUp Request**</span>
```yaml
{
    email: String
    password: String
    firstname: String
    lastname: String
    username: String
    country_code: Integer
    mobile_number: String
}
```

4. <span id="login">**Login Request**</span>
```yaml
{
    email: String
    password: String
}
```

5. <span id="forgot-password">**Forgot Password Request**</span>
```yaml
{
      email: String
}
```

6. <span id="reset-password">**Reset Password Request**</span>
```yaml
{
      email: String
      otp: String
      password: String
}
```

7. <span id="update-profile">**Update Profile Request**</span>
```yaml
{
    firstname: String
    lastname: String
    email: String
    country_code: Integer
    mobile_number: String
    username: String
    otp: String
}
```

9. <span id="change-password">**Change Password Request**</span>
```yaml
{
    current_password: String
    password: String
    confirm_password: String
}
```

### Response Object 

1. <span id="success-response">**Success Response**</span>
```yaml
{
    status: Boolean
    message: String
}
```

2. <span id="login-response">**SignUp / Login Response**</span>
```yaml
{
    status: Boolean
    message: String
    token: String
    user: UserObject
}
```
3. <span id="user-object">**User Object**</span>
```yaml
{
    id: Integer
    email: String
    firstname: String
    lastname: String
    username: String
    country_code: Integer
    mobile_number: String
}
```
