<template>
    <div>
        <div class="form-group mb-3" v-for="(item, index) in items">
            <div class="row">
                <div class="col-md-3 col-sm-5">
                    <div class="form-group">
                        <div class="ui-select-wrapper" style="z-index:1;">
                            <select :name="'facilities[' + item.id + '][id]'" v-model="item.id" class="ui-select">
                                <option value="">{{ __('select_facility') }}</option>
                                <option :value="facility.id" v-for="facility in availableFacilities(index)"
                                    :key="facility.id">{{ facility.name }}</option>
                            </select>
                            <svg class="svg-next-icon svg-next-icon-size-16">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-chevron"></use>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-5">
                    <div class="form-group">
                        <input type="text" :name="'facilities[' + item.id + '][distance]'" v-model="item.distance"
                            class="form-control" :placeholder="__('distance')">
                    </div>
                </div>
                <div class="col-md-3 col-sm-2">
                    <button class="btn btn-warning" type="button" @click="deleteRow(index)"><i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="form-group">
            <button v-if="availableFacilities.length > 0" class="btn btn-info" type="button" @click="addRow">{{
                __('add_new') }}</button>
        </div>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            items: [{ id: '', distance: '' }],
            removedFacilities: []
        };
    },
    mounted() {
        if (this.selected_facilities.length) {
            this.items = [];
            for (const item of this.selected_facilities) {
                this.items.push({ id: item.id, distance: item.distance });
            }
        }
    },
    props: {
        selected_facilities: {
            type: Array,
            default: () => [],
        },
        facilities: {
            type: Array,
            default: () => [],
        }
    },

    methods: {
        addRow: function () {
            this.items.push({ id: '', distance: '' });
        },
        deleteRow: function (index) {
            this.items.splice(index, 1);
        },
        availableFacilities: function (index) {
            const selectedIds = this.items.map(item => item.id);
            let result = this.facilities.filter(facility => facility.id === this.items[index].id || !selectedIds.includes(facility.id));
            return result
        }
    }
}
</script>
