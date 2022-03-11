<template>
    <div class="row justify-content-center">
        <div class="col">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Credit card number</label>
                    <input type="number" class="form-control" v-model="creditCard.card_number" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Credit card name</label>
                    <input type="text" class="form-control" v-model="creditCard.card_name" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Valid Until</label>
                    <input type="date" class="form-control" v-model="creditCard.valid_until" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">CCV</label>
                    <input type="text" pattern="\d*" maxlength="3" class="form-control" v-model="creditCard.ccv" />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Amount to transfer</label>
                    <input type="number" class="form-control" v-model="creditCard.amount" />
                </div>
            </div>
            <div class="form-row mt-2 mb-2">
                <div class="btn-group">
                    <button class="btn btn-primary" @click="paymentNab()">Pay with NAB</button>
                    <button class="btn btn-warning" @click="paymentAnz()">Pay with ANZ</button>
                </div>
            </div>
            <div class="row" v-if="error">
                <div class="col">
                    <span class="text-danger" v-text="error"></span>
                </div>
            </div>
            <div class="row" v-if="result">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            Result
                        </div>
                        <div class="card-body">
                            <span v-text="result"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" src="./DashboardComponent.vue.ts"></script>
