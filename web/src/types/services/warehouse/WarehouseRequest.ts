export interface WarehouseReadAnyPaginateRequest {
    with_trashed: boolean;
    company_id: string;
    branch_id?: string | null;
    search?: string | null;
    status?: string | number;
    refresh: boolean;
    page: number;
    per_page: number;
}

export interface WarehouseReadAnyGetRequest {
    with_trashed: boolean;
    company_id: string;
    branch_id?: string | null;
    search?: string | null;
    status?: string | number;
    refresh: boolean;
    limit: number;
}
