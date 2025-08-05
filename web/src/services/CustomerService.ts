import axios from "../axios";
import { useZiggyRouteStore } from "../stores/ziggy-route";
import { route, Config } from "ziggy-js";
import { Customer } from "@/types/models/Customer";
import { Resource } from "../types/resources/Resource";
import { Collection } from "../types/resources/Collection";
import { ServiceResponse } from "../types/services/ServiceResponse";
import { AxiosError, AxiosResponse, isAxiosError } from "axios";
import ErrorHandlerService from "./ErrorHandlerService";
import { ReadAnyRequest } from "../types/services/ServiceRequest";
import { StatusCode } from "../types/enums/StatusCode";
import { client, useForm } from "laravel-precognition-vue";

export default class CustomerService {
    private ziggyRoute: Config;
    private ziggyRouteStore = useZiggyRouteStore();

    private errorHandlerService;

    constructor() {
        this.ziggyRoute = this.ziggyRouteStore.getZiggy;
        this.errorHandlerService = new ErrorHandlerService();
    }

    public useCustomerCreateForm() {
        const url = route('api.post.db.customer.customer.save', undefined, true, this.ziggyRoute);

        client.axios().defaults.withCredentials = true;
        client.axios().defaults.withXSRFToken = true;
        const form = useForm('post', url, {
            company_id: '0',
            user_id: '0',
            customer_group_id: '0',
            code: '_AUTO_',
            name: '',
            zone: '',
            max_open_invoice: 0,
            max_outstanding_invoice: 0,
            max_invoice_age: 0,
            payment_term_type: '',
            payment_term: 0,
            is_member: false,
            taxable_enterprise: false,
            tax_id: '',
            status: '',
            remarks: '',
        });

        return form;
    }

    public async readAny(args: ReadAnyRequest): Promise<ServiceResponse<Collection<Array<Customer>> | Resource<Array<Customer>> | null>> {
        const result: ServiceResponse<Collection<Array<Customer>> | Resource<Array<Customer>> | null> = {
            success: false
        }

        try {
            const queryParams: Record<string, string | number | boolean> = {};
            queryParams['search'] = args.search ? args.search : '';
            if (args.company_id) queryParams['company_id'] = args.company_id;
            queryParams['refresh'] = args.refresh;
            queryParams['paginate'] = args.paginate;
            if (args.page) queryParams['page'] = args.page;
            if (args.per_page) queryParams['per_page'] = args.per_page;
            queryParams['with_trashed'] = args.with_trashed ?? false;

            const url = route('api.get.db.customer.customer.read_any', {
                _query: queryParams
            }, false, this.ziggyRoute);

            const response: AxiosResponse<Collection<Array<Customer>>> = await axios.get(url);

            if (response.status == StatusCode.OK) {
                result.success = true;
                result.data = response.data;
            }

            return result;
        } catch (e: unknown) {
            if (e instanceof Error && e.message.includes('Ziggy error')) {
                return this.errorHandlerService.generateZiggyUrlErrorServiceResponse(e.message);
            } else if (isAxiosError(e)) {
                return this.errorHandlerService.generateAxiosErrorServiceResponse(e as AxiosError);
            } else {
                return result;
            }
        }
    }

    public async read(ulid: string): Promise<ServiceResponse<Customer | null>> {
        const result: ServiceResponse<Customer | null> = {
            success: false
        }

        try {
            const url = route('api.get.db.customer.customer.read', {
                customer: ulid
            }, false, this.ziggyRoute);

            const response: AxiosResponse<Resource<Customer>> = await axios.get(url);

            if (response.status == StatusCode.OK) {
                result.success = true;
                result.data = response.data.data;
            }

            return result;
        } catch (e: unknown) {
            if (e instanceof Error && e.message.includes('Ziggy error')) {
                return this.errorHandlerService.generateZiggyUrlErrorServiceResponse(e.message);
            } else if (isAxiosError(e)) {
                return this.errorHandlerService.generateAxiosErrorServiceResponse(e as AxiosError);
            } else {
                return result;
            }
        }
    }

    public useCustomerEditForm(ulid: string) {
        const url = route('api.post.db.customer.customer.edit', ulid, true, this.ziggyRoute);

        client.axios().defaults.withCredentials = true;
        client.axios().defaults.withXSRFToken = true;
        const form = useForm('post', url, {
            company_id: '0',
            user_id: '0',
            customer_group_id: '0',
            code: '_AUTO_',
            name: '',
            zone: '',
            max_open_invoice: 0,
            max_outstanding_invoice: 0,
            max_invoice_age: 0,
            payment_term_type: '',
            payment_term: 0,
            is_member: false,
            taxable_enterprise: false,
            tax_id: '',
            status: '',
            remarks: '',
        });

        return form;
    }

    public async delete(ulid: string): Promise<ServiceResponse<boolean | null>> {
        const result: ServiceResponse<boolean | null> = {
            success: false,
        }

        try {
            const url = route('api.post.db.customer.customer.delete', ulid, false, this.ziggyRoute);

            const response: AxiosResponse<boolean | null> = await axios.post(url);

            if (response.status == StatusCode.OK) {
                result.success = true;
            }

            return result;
        } catch (e: unknown) {
            if (e instanceof Error && e.message.includes('Ziggy error')) {
                return this.errorHandlerService.generateZiggyUrlErrorServiceResponse(e.message);
            } else if (isAxiosError(e)) {
                return this.errorHandlerService.generateAxiosErrorServiceResponse(e as AxiosError);
            } else {
                return result;
            }
        }
    }
}