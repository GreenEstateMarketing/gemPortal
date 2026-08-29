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
                            <img  alt="media">
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
                    <div v-if="item.category_parent_id == '1'">
                        <div class="room-info mg-left pt-3" ><i class="fa fa-bed fa-2x bed-icon pr-2" aria-hidden="true"></i><b class="bed-no pr-2">{{ item.number_bedroom }}</b><i class="fa fa-bath fa-2x bath-icon pr-2" aria-hidden="true"></i><b class="bed-no pr-2">{{ item.number_bathroom }}</b><i class="fa fa-building  fa-2x pr-2 square-icon" aria-hidden="true"></i><b class="square-no pr-2">{{ item.square_text }}</b></div>
                    </div>
                    <div v-else>
                        <div class="room-info mg-left pt-3" ><i class="fa fa-building  square-icon pr-2" aria-hidden="true"></i><b class="square-no pr-2">{{ item.square_text }}</b></div>
                    </div>

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
    name: "related",

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
        city_id:{
            type: String,
            default: () => null,
            required: true
        },
        category_id:{
            type: String,
            default: () => null,
            required: true
        }
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
            if (this.project_id) {
                url += '&project_id=' + this.project_id;
            }

            if (this.category_id) {
                url += '&category_id=' + this.category_id;
            }

            if (this.city_id) {
                url += '&city_id=' + this.city_id;
            }


            axios.get(url)
                .then(res => {
                    this.data = res.data.data;
                    this.isLoading = false;
                });
        },
    },
    directives: {
        
    }
}
</script>
