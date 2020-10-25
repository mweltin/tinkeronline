export interface Setting {
    user_settings: {
        username: string,
        email: string,
        billing_info: string
    };
    child_settings: [
        {
            email: string,
            username: string,
            account_id: number,
            permissions: [
                {
                    name: string,
                    has_permission: string
                }
            ]
        }
    ];
    assets_to_approve: [
        {
            solution_id: number,
            challenge_id: number,
            asset_name: string,
            asset_temp_name: string,
            asset_type: string,
            approved: boolean
        }
    ]
};
