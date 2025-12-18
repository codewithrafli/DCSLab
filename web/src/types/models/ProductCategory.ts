import { Company } from "./Company";

export interface ProductCategory {
    id: string,
    ulid: string,
    code: string,
    name: string,
    type: number,
    company?: Company,
}

