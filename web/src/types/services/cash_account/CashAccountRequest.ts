export interface CashAccountReadAnyPaginateRequest {
    with_trashed: boolean;
    company_id: string;
    branch_id?: string | null;
    search?: string | null;
    include_id?: string;
    refresh: boolean;
    page: number;
    per_page: number;
}

export interface CashAccountReadAnyGetRequest {
    with_trashed: boolean;
    company_id: string;
    branch_id?: string | null;
    search?: string | null;
    include_id?: string;
    refresh: boolean;
    limit: number;
}
