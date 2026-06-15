export interface User {
    id: number;
    name: string;
    email: string;
    phone_number?: string;
    avatar_url?: string;
    role: 'super_admin' | 'admin' | 'hr' | 'mentor' | 'user';
    is_active: boolean | number;
    banned_at?: string | null;
    created_at: string;
    updated_at?: string;
    email_verified_at?: string | null;
    detail?: UserDetail;
    roles?: Role[];
    all_roles?: string[];
    created_at_human?: string;
}

export interface Role {
    id: number;
    name: string;
}

export interface UserDetail {
    education?: { institution: string }[];
    skills?: string[];
    phone_number?: string;
}

export interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

export interface PaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    links: PaginationLink[];
    path: string;
    per_page: number;
    to: number;
    total: number;
}

export interface PaginatedResponse<T> {
    data: T[];
    links: PaginationLink[];
    meta: PaginationMeta;
}
