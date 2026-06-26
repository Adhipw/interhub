export interface Company {
    id: number;
    name: string;
    slug?: string;
    logo_url?: string;
    location?: string;
    description?: string;
    industry?: string;
    website?: string;
    is_verified?: boolean;
    internships_count?: number;
}

export interface Internship {
    id: number;
    slug: string;
    title: string;
    type: string;
    location?: string;
    stipend?: string;
    company: Company;
    created_at?: string;
    updated_at?: string;
    description?: string;
    requirements?: string[];
    benefits?: string[];
    status?: 'draft' | 'published' | 'closed' | 'flagged' | 'archived';
    deadline_at_human?: string;
    created_at_human?: string;
    is_paid?: boolean;
    applications_count?: number;
    latitude?: number;
    longitude?: number;
    distance?: number | string;
}

export interface InternshipStats {
    total_internships: number;
    total_companies: number;
    total_placements: number;
    total_students: number;
}
