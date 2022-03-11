import Vue from 'vue'
import Component from 'vue-class-component'
import axios, {AxiosResponse} from "axios";
import * as _ from "lodash";
import {Prop} from "vue-property-decorator";
import {CreditCard} from "../models/credit-card";

@Component
export default class DashboardComponent extends Vue {
    private static readonly NAB_PAYMENT_URL = '/payment/nab';
    private static readonly ANZ_PAYMENT_URL = '/payment/anz'

    public creditCard: CreditCard = new CreditCard();
    public isProcessing: boolean = false;
    public error: string | null = null;
    public result: string | null = null;

    paymentNab(): void {
        this.pay(DashboardComponent.NAB_PAYMENT_URL);
    }

    paymentAnz(): void {
        this.pay(DashboardComponent.ANZ_PAYMENT_URL);
    }

    pay(url: string): void {
        axios
            .post(url, this.creditCard)
            .then((response: AxiosResponse) => {
                this.result = response.data;
            })
            .catch((error) => {
                this.error = error.response.data;
            });
    }
}
