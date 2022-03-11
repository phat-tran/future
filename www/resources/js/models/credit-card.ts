export class CreditCard {
    constructor(
        public card_name: string | null = null,
        public card_number: string | null = null,
        public valid_until: string | null = null,
        public ccv: number | null = null,
        public amount: number | null = null,
    ) {
    }
}
