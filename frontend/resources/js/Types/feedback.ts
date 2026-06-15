import type { Application } from './application';

export interface Feedback {
    id: number;
    application_id: number;
    user_id: number; // mentor id
    content: string;
    created_at: string;
    updated_at: string;
    application: Application;
}
