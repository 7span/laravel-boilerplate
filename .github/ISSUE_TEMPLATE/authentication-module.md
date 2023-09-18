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
| otp |  varchar(32) | No ||
| opt_generated_at |  timestamp | No | Timestamp when the OTP has been generated. |
| opt_expired_at |  timestamp | No | Define the expiration timestamp of the OTP as 15 minutes after its creation time. |
| forget_password_code | varchar(8) | No | The passcode will be stored here when a user attempts to reset their password. | 
| created_at | timestamp | Yes | Created timestamp |
| updated_at | timestamp | No |  |
| deleted_at | timestamp | No |  |

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
| ...  | ... | ... | All the other additional user-related fields | 

## Role and Permission

By default, the system should have an admin and user role. Kindly use the [Laravel Permission](https://spatie.be/docs/laravel-permission/v5/introduction) package for that. 

## Endpoints

> [!IMPORTANT]
> **For all endpoints that require authentication, kindly ensure that the token is included in the Authorization header of the API request.**

**Base URL : `api/v1/`**

| Endpoint  |  Method | Argument  | Response | Authentication Required | Description |
|---|---|---|---|---|---|
| send-otp | Post |  [Send OTP](#send-otp)  | [Send OTP response](#success-response)  | No |  Send the OTP to verify the mobile number.  |
| verify-otp | Post | [Verify OTP](#verify-otp) | [Verify OTP Response](#success-response)  | No |  Verify the OTP |
| signup | Post | [SignUp Request](#signup)  | [SignUp response](#login-response)  | No | In the signup process, an OTP will be initially sent to the registered phone number using **Send OTP API**, the user will add the received OTP on a signup process and this OTP should be included in the signup request. If the provided OTP is valid, the signup process will be successfully completed. There is no requirement to use a separate **Verify OTP** step in the signup process. |
| login |  Post |   [Login Request](#login-request)  | [Login response](#login-response)  | No | This endpoint is intended for user login using an **email** . |
| login/mobile |  Post |   [Login Request](#login-with-mobile)  | [Login response](#login-response)  | No | This endpoint is intended for user login using a **mobile number**. |
| forgot-password | Post |  [Forgot Password Request](#forgot-password) | [Forgot Password Response](#success-response) | No | It requires either a mobile number or an email address to be provided. |
| reset-password |  Post |   [Reset password Request](#reset-password) | [Reset Password Response](#success-response) | No | It requires either a mobile number or an email address to be provided. |
| me |  Get |  |  [User Response](#user-object) | Yes | To retrieve the profile of the logged-in user, please include the token in the Authorization header of the API request. |
| me |  Post | [Update Profile Request](#update-profile) |  [User Response](#user-object) | Yes | If the login flow includes the option to log in with a mobile number, users can update their email, and if the login flow includes the option to log in with an email, users can update their mobile number. Both scenarios are governed by the 'required_if' validation. |
| change-password |  Post | [Change Password Request](#change-password) |  [Success Response](#success-response) | Yes |  |


### Request Object

1. <span id="send-otp">**Send OTP params**</span>
```yaml
{
    country_code: Integer
    mobile_number: String 
}
```

2. <span id="verify-otp">**Verify OTP params**</span>
```yaml
{
    country_code: Integer
    mobile_number: String
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
    otp: String
}
```
> It requires either a mobile number or an email address to be provided.

4. <span id="login">**Login Request**</span>
```yaml
{
    email: String
    password: String
}
```

5. <span id="login-with-mobile">**Login Request (With Mobile)**</span>
```yaml
{
    country_code: Integer
    mobile_number: String
    password: String
}
```

6. <span id="forgot-password">**Forgot Password Request**</span>
```yaml
{
      country_code: Integer
      mobile_number: String
      email: String
}
```

> It requires either a mobile number or an email address to be provided.

7. <span id="reset-password">**Reset Password Request**</span>
```yaml
{
      country_code: Integer
      mobile_number: String
      email: String
      passcode: String
      password: String
}
```
> It requires either a mobile number or an email address to be provided.

8. <span id="update-profile">**Update Profile Request**</span>
```yaml
{
    firstname: String
    lastname: String
    email: String ## Users can update their email address only if they have logged in using their mobile number.
    country_code: Integer ## Users can update their country code only if they have logged in using their email.
    mobile_number: String ## Users can update their mobile number only if they have logged in using their email.
}
```

9. <span id="change-password">**Change Password Request**</span>
```yaml
{
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
