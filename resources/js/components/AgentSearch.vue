<template>
  <div class="agent-search">
    <div class="agent-search__filters">
      <div class="agent-search__row agent-search__row--primary">
        <input
          type="text"
          class="form-control agent-search__keyword"
          v-model="keyword"
          @keyup.enter="search"
          placeholder="Search agents by name..."
        />
        <button type="button" class="btn btn-primary" @click="requestNearMe" :disabled="locating">
          <i class="fas fa-location-arrow"></i> {{ locating ? 'Locating...' : 'Near Me' }}
        </button>
        <button
          type="button"
          class="btn btn-outline agent-search__filters-toggle d-md-none"
          data-toggle="collapse"
          data-target="#agent-filters-collapse"
        >
          Filters
        </button>
      </div>

      <div class="collapse d-md-flex agent-search__row" id="agent-filters-collapse">
        <select v-model="countryId" @change="onCountryChange" class="form-control select--arrow">
          <option value="">All Countries</option>
          <option v-for="country in countryList" :key="country.id" :value="country.id">{{ country.name }}</option>
        </select>

        <select v-model="cityId" class="form-control select--arrow">
          <option value="">All Cities</option>
          <option v-for="city in cityList" :key="city.id" :value="city.id">{{ city.name }}</option>
        </select>

        <div class="dropdown agent-search__dropdown">
          <button type="button" class="btn btn-outline dropdown-toggle" data-toggle="dropdown">
            Languages<template v-if="languageIds.length"> ({{ languageIds.length }})</template>
          </button>
          <div class="dropdown-menu agent-search__panel">
            <label v-for="language in languageList" :key="language.id" class="checkbox-inline d-block">
              <input type="checkbox" :value="language.id" v-model="languageIds" /> {{ language.name }}
            </label>
          </div>
        </div>

        <div class="dropdown agent-search__dropdown">
          <button type="button" class="btn btn-outline dropdown-toggle" data-toggle="dropdown">
            Specialty<template v-if="categoryIds.length"> ({{ categoryIds.length }})</template>
          </button>
          <div class="dropdown-menu agent-search__panel">
            <label v-for="category in categoryList" :key="category.id" class="checkbox-inline d-block">
              <input type="checkbox" :value="category.id" v-model="categoryIds" /> {{ category.name }}
            </label>
          </div>
        </div>

        <div class="dropdown agent-search__dropdown">
          <button type="button" class="btn btn-outline dropdown-toggle" data-toggle="dropdown">
            Experience ({{ minExperience }}-{{ maxExperience }}y)
          </button>
          <div class="dropdown-menu agent-search__panel agent-search__slider-panel">
            <div class="agent-search__slider">
              <input type="range" min="1" max="25" v-model.number="minExperience" @input="clampMin" />
              <input type="range" min="1" max="25" v-model.number="maxExperience" @input="clampMax" />
            </div>
            <div class="agent-search__slider-labels">
              <span>{{ minExperience }}y</span>
              <span>{{ maxExperience }}y</span>
            </div>
          </div>
        </div>

        <button type="button" class="btn btn-primary" @click="search">Search</button>
      </div>
    </div>

    <div class="agent-search__results">
      <div v-if="isLoading" class="text-center py-5">
        <div class="half-circle-spinner">
          <div class="circle circle-1"></div>
          <div class="circle circle-2"></div>
        </div>
      </div>
      <div v-else-if="!data.length" class="text-center py-5">No agents found</div>
      <div v-else class="row">
        <div class="col-12 col-sm-6 col-lg-4" v-for="agent in data" :key="agent.id">
          <figure class="agent-card">
            <img :src="agent.avatar" :alt="agent.name" class="agent-card__avatar" />
            <figcaption>
              <h4>{{ agent.name }}</h4>
              <small v-if="agent.description">{{ agent.description }}</small>
              <p v-if="agent.distance !== null" class="agent-card__distance">{{ agent.distance }} km away</p>
              <p v-if="agent.years_of_experience">{{ agent.years_of_experience }} yrs experience</p>
              <p v-if="agent.languages.length">{{ agent.languages.join(', ') }}</p>
              <p v-if="agent.specialties.length">{{ agent.specialties.join(', ') }}</p>
              <ul>
                <li v-if="agent.phone && !revealed[agent.id]">
                  <button type="button" class="btn btn-primary btn-sm" @click="showContact(agent)">Show Contact</button>
                </li>
                <li v-if="agent.phone && revealed[agent.id]"><i class="fa fa-phone"></i> {{ agent.phone }}</li>
                <li>
                  <a v-if="agent.properties_count > 0" :href="'/agent-detail/' + agent.username">
                    properties by this agent <i class="fa fa-home"></i> {{ agent.properties_count }}
                  </a>
                  <span v-else>properties by this agent <i class="fa fa-home"></i> 0</span>
                </li>
              </ul>
            </figcaption>
          </figure>
        </div>
      </div>
      <pagination :data="links" @pagination-change-page="fetchAgents"></pagination>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    url: {
      type: String,
      required: true,
    },
    citiesUrl: {
      type: String,
      required: true,
    },
    countries: {
      type: String,
      default: '[]',
    },
    cities: {
      type: String,
      default: '[]',
    },
    languages: {
      type: String,
      default: '[]',
    },
    categories: {
      type: String,
      default: '[]',
    },
    defaultCountryId: {
      type: [String, Number],
      default: '',
    },
    defaultCityId: {
      type: [String, Number],
      default: '',
    },
  },
  data() {
    return {
      keyword: '',
      countryId: this.defaultCountryId || '',
      cityId: this.defaultCityId || '',
      languageIds: [],
      categoryIds: [],
      minExperience: 1,
      maxExperience: 25,
      lat: null,
      lng: null,
      locating: false,
      isLoading: true,
      data: [],
      links: {},
      revealed: {},
      countryList: JSON.parse(this.countries),
      cityList: JSON.parse(this.cities),
      languageList: JSON.parse(this.languages),
      categoryList: JSON.parse(this.categories),
    };
  },
  mounted() {
    this.fetchAgents();
  },
  methods: {
    onCountryChange() {
      this.cityId = '';
      this.cityList = [];

      if (!this.countryId) {
        return;
      }

      axios.get(this.citiesUrl, { params: { country_id: this.countryId } }).then((response) => {
        this.cityList = response.data.data;
      });
    },
    clampMin() {
      if (this.minExperience > this.maxExperience) {
        this.maxExperience = this.minExperience;
      }
    },
    clampMax() {
      if (this.maxExperience < this.minExperience) {
        this.minExperience = this.maxExperience;
      }
    },
    requestNearMe() {
      if (!navigator.geolocation) {
        return;
      }

      this.locating = true;

      navigator.geolocation.getCurrentPosition(
        (position) => {
          this.lat = position.coords.latitude;
          this.lng = position.coords.longitude;
          this.locating = false;
          this.search();
        },
        () => {
          this.locating = false;
        }
      );
    },
    search() {
      this.fetchAgents(1);
    },
    showContact(agent) {
      this.$set(this.revealed, agent.id, true);
    },
    fetchAgents(page = 1) {
      this.isLoading = true;

      axios
        .get(this.url, {
          params: {
            page,
            keyword: this.keyword || undefined,
            country_id: this.countryId || undefined,
            city_id: this.cityId || undefined,
            language_ids: this.languageIds.length ? this.languageIds : undefined,
            category_ids: this.categoryIds.length ? this.categoryIds : undefined,
            min_experience: this.minExperience,
            max_experience: this.maxExperience,
            lat: this.lat || undefined,
            lng: this.lng || undefined,
          },
        })
        .then((response) => {
          this.data = response.data.data;
          this.links = response.data.meta;
          this.isLoading = false;
        });
    },
  },
};
</script>

<style scoped>
.agent-search__row {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  margin-bottom: 15px;
}

.agent-search__keyword {
  flex: 1 1 240px;
}

.agent-search__dropdown .dropdown-menu {
  padding: 15px;
}

.agent-search__panel {
  min-width: 220px;
  max-height: 260px;
  overflow-y: auto;
}

.agent-search__slider-panel {
  min-width: 260px;
}

.agent-search__slider {
  position: relative;
  height: 30px;
}

.agent-search__slider input[type='range'] {
  position: absolute;
  width: 100%;
  top: 8px;
  pointer-events: none;
  -webkit-appearance: none;
  background: transparent;
}

.agent-search__slider input[type='range']::-webkit-slider-thumb {
  pointer-events: auto;
  -webkit-appearance: none;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: var(--primary-color);
  cursor: pointer;
}

.agent-search__slider input[type='range']::-moz-range-thumb {
  pointer-events: auto;
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: var(--primary-color);
  cursor: pointer;
  border: none;
}

.agent-search__slider-labels {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}

.agent-card {
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
  border-radius: 5px;
  padding: 15px;
  margin-bottom: 20px;
}

.agent-card__avatar {
  width: 100%;
  max-width: 160px;
  border-radius: 50%;
  display: block;
  margin: 0 auto 10px;
}

@media (max-width: 768px) {
  .agent-search__row {
    flex-direction: column;
    align-items: stretch;
  }

  .agent-search__dropdown .dropdown-menu,
  .agent-search__panel {
    width: 100%;
    min-width: 0;
  }
}
</style>
