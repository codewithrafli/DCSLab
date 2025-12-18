import axios from "../axios";
import { useZiggyRouteStore } from "../stores/ziggy-route";
import { route, Config } from "ziggy-js";
import { ProductCategory } from "@/types/models/ProductCategory";
import { Resource } from "../types/resources/Resource";
import { Collection } from "../types/resources/Collection";
import { ServiceResponse } from "../types/services/ServiceResponse";
import { AxiosError, AxiosResponse, isAxiosError } from "axios";
import ErrorHandlerService from "./ErrorHandlerService";
import { ReadAnyRequest } from "../types/services/ServiceRequest";
import { StatusCode } from "../types/enums/StatusCode";
import { client, useForm } from "laravel-precognition-vue";

export default class ProductCategoryService {
    private ziggyRoute: Config;
    private ziggyRouteStore = useZiggyRouteStore();

    private errorHandlerService;

    constructor() {
        this.ziggyRoute = this.ziggyRouteStore.getZiggy;

        this.errorHandlerService = new ErrorHandlerService();
    }

    public useProductCategoryCreateForm() {
        const url = route('api.post.db.product.product_category.save', undefined, true, this.ziggyRoute);

        client.axios().defaults.withCredentials = true;
        client.axios().defaults.withXSRFToken = true;
        const form = useForm('post', url, {
            company_id: '0',
            code: '_AUTO_',
            name: '',
            type: 1,
        });

        return form;
    }

    public async readAny(args: ReadAnyRequest): Promise<ServiceResponse<Collection<Array<ProductCategory>> | Resource<Array<ProductCategory>> | null>> {
        const result: ServiceResponse<Collection<Array<ProductCategory>> | Resource<Array<ProductCategory>> | null> = {
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

            const url = route('api.get.db.product.product_category.read_any', {
                _query: queryParams
            }, false, this.ziggyRoute);

            const response: AxiosResponse<Collection<Array<ProductCategory>>> = await axios.get(url);

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

    public async read(ulid: string): Promise<ServiceResponse<ProductCategory | null>> {
        const result: ServiceResponse<ProductCategory | null> = {
            success: false
        }

        try {
            const url = route('api.get.db.product.product_category.read', {
                product_category: ulid
            }, false, this.ziggyRoute);

            const response: AxiosResponse<Resource<ProductCategory>> = await axios.get(url);

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

    public useProductCategoryEditForm(ulid: string) {
        const url = route('api.post.db.product.product_category.edit', ulid, true, this.ziggyRoute);

        client.axios().defaults.withCredentials = true;
        client.axios().defaults.withXSRFToken = true;
        const form = useForm('post', url, {
            company_id: '0',
            code: '_AUTO_',
            name: '',
            type: 1,
        });

        return form;
    }

    public async delete(ulid: string): Promise<ServiceResponse<boolean | null>> {
        const result: ServiceResponse<boolean | null> = {
            success: false,
        }

        try {
            const url = route('api.post.db.product.product_category.delete', ulid, false, this.ziggyRoute);

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


