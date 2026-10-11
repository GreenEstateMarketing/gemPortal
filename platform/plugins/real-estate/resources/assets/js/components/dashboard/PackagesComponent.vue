<template>
    <div>
        <div class="alert alert-info current-package">
            <p>{{ __('your_credits')}}: <strong>{{ account.credits }} {{ __('credits')}}</strong></p>
        </div>
        <div class="packages-listing">
            <div class="row flex-items-xs-middle flex-items-xs-center">
                <div style="margin: auto; width:30px;" v-if="isLoading">
                    <half-circle-spinner
                        :animation-duration="1000"
                        :size="15"
                        color="#808080"
                    />
                </div>
                <div :class="data.length === 2 ? 'col-xs-12 col-lg-6' : 'col-xs-12 col-lg-4'" v-for="item in data" :key="item.id" v-if="!isLoading && data.length && account" style="margin-top: 30px">
                    <div class="credit-card" :class="{ 'credit-card--recommended': item.is_recommended }">
                        <div class="credit-card__ribbon" v-if="item.is_recommended">{{ __('best_value') }}</div>
                        <div class="credit-card__body">
                            <h4 class="credit-card__title">{{ item.name }}</h4>

                            <div class="credit-card__credits" v-if="item.price">
                                <strong>{{ item.number_of_listings }}</strong> {{ __('credits_included') }}
                            </div>
                            <div class="credit-card__credits" v-if="!item.price">
                                {{ __('free') }} {{ item.number_of_listings }} {{ __('posts') }}
                            </div>

                            <template v-if="item.price">
                                <div class="credit-card__discount-pill" v-if="item.discount_percent > 0">
                                    {{ item.discount_percent }}% {{ __('off') }}
                                </div>

                                <div class="credit-card__price">
                                    <span class="credit-card__price-original" v-if="item.discount_percent > 0">{{ item.original_price_text }}</span>
                                    <span class="credit-card__price-current">{{ item.price_text }}</span>
                                </div>

                                <div class="credit-card__savings" v-if="item.discount_percent > 0">
                                    {{ __('you_save') }} {{ item.savings_text }}
                                </div>

                                <div class="credit-card__per-post">{{ item.price_per_post_text }} / {{ __('per_post') }}</div>
                            </template>
                            <div class="credit-card__price" v-if="!item.price">&mdash;</div>

                            <button
                                class="btn btn-primary credit-card__cta"
                                :class="{ 'button-loading': isSubscribing && currentPackageId === item.id }"
                                @click="postSubscribe(item.id)"
                                :disabled="isSubscribing"
                            >{{ __('buy_now') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {HalfCircleSpinner} from 'epic-spinners'

    export default {
        components: {
            HalfCircleSpinner
        },

        data: function() {
            return {
                isLoading: true,
                isSubscribing: false,
                data: [],
                account: {},
                currentPackageId: null,
            };
        },

        mounted() {
            this.getData();
        },

        props: {
            url: {
                type: String,
                default: () => null,
                required: true
            },
            subscribe_url: {
                type: String,
                default: () => null,
                required: true
            },
        },

        methods: {
            getData() {
                this.data = [];
                this.isLoading = true;
                axios.get(this.url)
                    .then(res => {
                        if (res.data.error) {
                            Botble.showError(res.data.message);
                        } else {
                            this.data = res.data.data.packages;
                            this.account = res.data.data.account;
                        }
                        this.isLoading = false;
                    });
            },

            postSubscribe(id) {
                this.isSubscribing = true;
                this.currentPackageId = id;
                axios.put(this.subscribe_url, {id: id})
                    .then(res => {
                        if (res.data.error) {
                            Botble.showError(res.data.message);
                        } else {
                            if (res.data.data && res.data.data.next_page) {
                                window.location.href = res.data.data.next_page;
                            } else {
                                this.account = res.data.data;
                                Botble.showSuccess(res.data.message);
                                this.getData();
                            }
                        }
                        this.isSubscribing = false;
                    });
            }
        }
    }
</script>

<style scoped>
.credit-card {
    position: relative;
    height: 100%;
    display: flex;
    flex-direction: column;
    background: #fff;
    border: 1px solid #e2e5e8;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}

.credit-card:hover {
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.credit-card--recommended {
    border: 2px solid #e9b160;
    box-shadow: 0 8px 20px rgba(233, 177, 96, 0.25);
}

.credit-card__ribbon {
    position: absolute;
    top: 14px;
    right: -32px;
    width: 150px;
    transform: rotate(45deg);
    background: #e9b160;
    color: #fff;
    text-align: center;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.03em;
    padding: 4px 0;
    text-transform: uppercase;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.credit-card__body {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 28px 22px;
    text-align: center;
}

.credit-card__title {
    font-size: 19px;
    font-weight: 700;
    margin-bottom: 10px;
}

.credit-card__credits {
    color: #6b6b6b;
    font-size: 14px;
    margin-bottom: 14px;
}

.credit-card__credits strong {
    color: #111;
    font-size: 16px;
}

.credit-card__discount-pill {
    display: inline-block;
    align-self: center;
    background: #e8453c;
    color: #fff;
    font-weight: 700;
    font-size: 12px;
    letter-spacing: 0.02em;
    padding: 4px 12px;
    border-radius: 20px;
    margin-bottom: 10px;
}

.credit-card__price {
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 8px;
    margin-bottom: 6px;
}

.credit-card__price-original {
    color: #9a9a9a;
    text-decoration: line-through;
    font-size: 15px;
}

.credit-card__price-current {
    color: #111;
    font-size: 26px;
    font-weight: 800;
}

.credit-card__savings {
    color: #1a8f4c;
    font-weight: 600;
    font-size: 13px;
    margin-bottom: 4px;
}

.credit-card__per-post {
    color: #9a9a9a;
    font-size: 12px;
    margin-bottom: 18px;
}

.credit-card__cta {
    margin-top: auto;
    width: 100%;
    font-weight: 700;
    padding: 10px 0;
}
</style>
