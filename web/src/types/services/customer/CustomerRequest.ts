export interface CustomerReadAnyPaginateRequest {
    with_trashed: boolean;

    company_id: string;
    search?: string | null;
    status?: number | null;
    include_id?: string;

    refresh: boolean;
    page: number;
    per_page: number;
}

export interface CustomerReadAnyGetRequest {
    with_trashed: boolean;

    company_id: string;
    search?: string | null;
    status?: number | null;
    include_id?: string;

    refresh: boolean;
    limit: number;
}

