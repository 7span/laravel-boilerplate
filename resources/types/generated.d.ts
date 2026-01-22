declare namespace App.Enums {
    export type UserOtpFor = 'forgot_password' | 'email_verification';
    export type UserStatus = 'active' | 'inactive';
}

declare namespace App.Models {
    export type User = {
        incrementing: boolean;
        preventsLazyLoading: boolean;
        exists: boolean;
        wasRecentlyCreated: boolean;
        timestamps: boolean;
        usesUniqueIds: boolean;
    };
    export type UserDevice = {
        incrementing: boolean;
        preventsLazyLoading: boolean;
        exists: boolean;
        wasRecentlyCreated: boolean;
        timestamps: boolean;
        usesUniqueIds: boolean;
    };
}
