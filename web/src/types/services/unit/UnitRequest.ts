export interface UnitReadAnyPaginateRequest {
    with_trashed: boolean,

    search?: string | null,
    company_id: string,
    include_id?: string,

    refresh: boolean,
    page: number,
    per_page: number,
}

export interface UnitReadAnyGetRequest {
    with_trashed: boolean,

    search?: string | null,
    company_id: string,
    include_id?: string,

    refresh: boolean,
    limit: number,
}
