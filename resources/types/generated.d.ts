declare namespace App.Data {
    export type MediaData = {
        id: number;
        disk: string | null;
        directory: string | null;
        filename: string | null;
        extension: string | null;
        mime_type: string | null;
        aggregate_type: string | null;
        size: number | null;
        created_at: number | null;
        updated_at: number | null;
        url: string | null;
        cdn_url: string | null;
    };
    export type NotificationData = {
        id: string;
        user_id: number;
        sent_by: number | null;
        title: string | null;
        description: string | null;
        type: string | null;
        notifiable_type: string;
        notifiable_id: number;
        data: { [key: string]: any };
        read_at: number | null;
        created_at: number | null;
        updated_at: number | null;
        user?: App.Data.UserData | null;
        sender?: App.Data.UserData | null;
        sender_name?: string;
        buyer_name?: string;
        user_with_type?: string;
    };
    export type PostData = {
        id: number;
        user_id: number;
        title: string;
        body: string | null;
        published_at: number | null;
        user?: App.Data.UserData | null;
        created_at: number | null;
        updated_at: number | null;
        display_title?: string;
    };
    export type PostRequestData = {
        title?: string;
        body?: string | null;
        published_at?: number | null;
    };
    export type UserData = {
        id: number;
        first_name: string | null;
        last_name: string | null;
        username: string | null;
        email: string | null;
        status: App.Enums.UserStatus | null;
        country_code: string | null;
        mobile_no: string | null;
        email_verified_at: number | null;
        last_login_at: number | null;
        created_at: number | null;
        name: string;
        display_status: string;
        display_mobile_no: string;
        profile?: App.Data.MediaData | null;
    };
    export type UserDeviceData = {
        id: number;
        user_id: number | null;
        onesignal_player_id: string | null;
        device_id: string | null;
        device_type: string | null;
        created_at: number | null;
        updated_at: number | null;
    };
}
declare namespace App.Enums {
    export type UserOtpFor = 'forgot_password' | 'email_verification';
    export type UserStatus = 'active' | 'inactive';
}
