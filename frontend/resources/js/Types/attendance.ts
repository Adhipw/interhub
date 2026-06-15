import type { User } from './user';
import type { Application } from './application';

export interface Attendance {
    id: number;
    user_id: number;
    application_id: number;
    check_in_at: string;
    check_out_at: string | null;
    status: 'present' | 'absent' | 'late' | 'excused';
    latitude?: number;
    longitude?: number;
    user: User;
    application: Application;
    created_at: string;
    updated_at: string;
}

export interface LiveLocation {
    lat: number;
    lng: number;
    updated_at: string;
}
