export interface BranchReadAnyPaginateRequest {
    with_trashed: boolean,
    
    search?: string | null,
    company_id: string,
    is_main?: boolean,
    status?: string | number,
    include_id?: string,
    
    refresh: boolean,    
    page: number,
    per_page: number,
}

export interface BranchReadAnyGetRequest {
    with_trashed: boolean,
    
    search?: string | null,
    company_id: string,
    is_main?: boolean,
    status?: string | number,
    include_id?: string,

    refresh: boolean,
    limit: number,
}
