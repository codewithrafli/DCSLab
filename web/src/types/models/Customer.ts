import { Company } from "./Company";
import { CustomerGroup } from "./CustomerGroup";
import { User } from "./User";

export interface Customer {
    id: string,
    ulid: string,
    company: Company,
    user: User,
    code: string,
    isMember: boolean,
    name: string,
    group: CustomerGroup,
    zone: string,
    maxOpenInvoice: number,
    maxOutstandingInvoice: number,
    maxInvoiceAge: number,
    paymentTermType: string,
    paymentTerm: number,
    taxableEnterprise: boolean,
    taxId: string,
    status: string,
    remarks: string,
}

