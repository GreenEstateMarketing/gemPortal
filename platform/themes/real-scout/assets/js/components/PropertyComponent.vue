<template>
    <div class="row rowm10">
        <div class="half-circle-spinner" v-if="isLoading">
            <div class="circle circle-1"></div>
            <div class="circle circle-2"></div>
        </div>
        <div v-if="show_empty_string && !isLoading && !data.length" class="col-12 text-center">
            <span>{{ __('No property found') }}!</span>
        </div>
        <div class="col-sm-4 col-md-3 colm10" v-for="item in data" :key="item.id" v-if="!isLoading && data.length" v-favorites>
            <div class="hourseitem">
                <div class="blii">
                    <div class="img"><img class="thumb" :data-src="item.image" :src="item.image" :alt="item.name">
                    </div>
                    <a :href="item.url" class="linkdetail"></a>
                    <div class="media-count-wrapper">
                        <div class="media-count">
                            <img :src="themeUrl('images/media-count.svg')" alt="media">
                            <span>{{ item.images.length }}</span>
                        </div>
                    </div>
                    <div class="status" v-html="$sanitize(item.status_html, {allowedTags: ['span'], allowedAttributes: {'span': ['class']}})"></div>
                    <ul class="item-price-wrap hide-on-list"><li class="h-type"><span>{{ item.category_name }}</span></li> <li class="item-price">{{ item.price }}</li></ul>
                </div>
                <div class="info">
                    <a href="#" class="text-orange heart add-to-wishlist" :data-id="item.id" :title="__('I care about this property!!!')"><i class="far fa-heart"></i></a>
                    <h3><a :href="item.url">{{ item.name }}</a></h3>
                    <p class="city"><i class="fas fa-map-marker-alt" style="opacity: 0.7"></i>  {{ item.location }}</p>
                    <p class="threemt bold500">
                    <div class="room-info pt-3" ><b class="pr-2 bed-no">{{ item.number_bedroom }}</b><i class="fa fa-bed fa-2x bed-icon" aria-hidden="true"></i><b class="pr-2 bath-no">{{ item.number_bathroom }}</b><i class="fa fa-bath fa-2x bath-icon" aria-hidden="true"></i><i class="fa fa-building  square-icon" aria-hidden="true"></i><b class="pr-2 square-no">{{ item.square_text }}</b></div>

                    <!--                        <span data-toggle="tooltip" data-placement="top" :data-original-title="__('Number of rooms')" v-if="item.number_bedroom"> <i><img :src="themeUrl('images/bed.svg')" alt="icon"></i> <i class="vti">{{ item.number_bedroom }}</i> </span>
                                            <span data-toggle="tooltip" data-placement="top" :data-original-title="__('Number of rest rooms')" v-if="item.number_bathroom">  <i><img :src="themeUrl('images/bath.svg')" alt="icon"></i> <i class="vti">{{ item.number_bathroom }}</i></span>
                                            <span data-toggle="tooltip" data-placement="top" :data-original-title="__('Square')" v-if="item.square"> <i><img :src="themeUrl('images/area.svg')" alt="icon"></i> <i class="vti">{{ item.square_text }}</i> </span>-->

                </div>
            </div>
        </div>
    </div>
</template>

<script>

    export default {

        data: function() {
            return {
                isLoading: true,
                data: []
            };
        },

        mounted() {
            this.getProperties();
        },

        props: {
            url: {
                type: String,
                default: () => null,
                required: true
            },
            type: {
                type: String,
                default: () => 'rent',
            },
            property_id: {
                type: Number,
                default: () => null,
            },
            project_id: {
                type: Number,
                default: () => null,
            },
            show_empty_string: {
                type: Boolean,
                default: () => false
            },
        },

        methods: {
            getProperties() {
                this.data = [];
                this.isLoading = true;
                let url = this.url + '?type=' + this.type;

                if (this.property_id) {
                    url += '&property_id=' + this.property_id;
                }

                if (this.project_id) {
                    url += '&project_id=' + this.project_id;
                }

                axios.get(url)
                    .then(res => {
                        this.data = res.data.data;
                        this.isLoading = false;
                    });
            },
        },
        directives: {
            favorites: function () {
                let getCookie = function (cname) {
                    var name = cname + '=';
                    var ca = document.cookie.split(';');
                    for (var i = 0; i < ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) == ' ') {
                            c = c.substring(1);
                        }
                        if (c.indexOf(name) == 0) {
                            return c.substring(name.length, c.length);
                        }
                    }
                    return '';
                }

                let cookieName = window.currentLanguage + '_wishlist';
                let wishListCookies = decodeURIComponent(getCookie(cookieName));

                if (wishListCookies != null && wishListCookies != undefined && !!wishListCookies) {
                    var arrList = JSON.parse(wishListCookies);
                    var countWishlist = arrList.length;

                    $('.wishlist-count').text(countWishlist);
                    if (countWishlist > 0) {
                        $('.add-to-wishlist').removeClass('far fa-heart');
                        $.each(arrList, function (key, value) {
                            if (value != null) {
                                $(document).find(`.add-to-wishlist[data-id=${value.id}] i`).addClass('fas fa-heart');
                            }
                        });
                    }
                }
            }
        }
    }
</script>
