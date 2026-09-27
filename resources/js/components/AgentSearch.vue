<template>
  <div class="agent-search">
    <div class="agent-search__card">
      <div class="agent-search__row agent-search__row--primary">
        <div class="agent-search__keyword-field">
          <label class="agent-search__label">Search by name</label>
          <input
            type="text"
            class="agent-search__input"
            v-model="keyword"
            @keyup.enter="search"
            placeholder="e.g. Charles Leclerc"
          />
        </div>
        <button type="button" class="agent-btn agent-btn--outline" @click="requestNearMe" :disabled="locating">
          <i class="fas fa-location-arrow"></i> {{ locating ? 'Locating…' : 'Near Me' }}
        </button>
        <button
          type="button"
          class="agent-btn agent-btn--ghost agent-search__filters-toggle d-md-none"
          data-toggle="collapse"
          data-target="#agent-filters-collapse"
        >
          <i class="fas fa-sliders-h"></i> Filters
        </button>
      </div>

      <div class="collapse d-md-block" id="agent-filters-collapse">
        <div class="agent-search__row agent-search__row--location">
          <div class="agent-search__field">
            <label class="agent-search__label">Country</label>
            <select v-model="countryId" @change="onCountryChange" class="agent-search__select">
              <option value="">All Countries</option>
              <option v-for="country in countryList" :key="country.id" :value="country.id">{{ country.name }}</option>
            </select>
          </div>
          <div class="agent-search__field-divider"></div>
          <div class="agent-search__field">
            <label class="agent-search__label">City</label>
            <select v-model="cityId" class="agent-search__select">
              <option value="">All Cities</option>
              <option v-for="city in cityList" :key="city.id" :value="city.id">{{ city.name }}</option>
            </select>
          </div>
        </div>

        <div class="agent-search__row agent-search__row--filters">
          <div class="agent-search__dropdown">
            <button type="button" class="agent-btn agent-btn--ghost dropdown-toggle" data-toggle="dropdown">
              Languages<template v-if="languageIds.length"> ({{ languageIds.length }})</template>
            </button>
            <div class="dropdown-menu agent-search__panel">
              <label v-for="language in languageList" :key="language.id" class="agent-search__checkbox">
                <input type="checkbox" :value="language.id" v-model="languageIds" /> {{ language.name }}
              </label>
            </div>
          </div>

          <div class="agent-search__dropdown">
            <button type="button" class="agent-btn agent-btn--ghost dropdown-toggle" data-toggle="dropdown">
              Specialty<template v-if="categoryIds.length"> ({{ categoryIds.length }})</template>
            </button>
            <div class="dropdown-menu agent-search__panel">
              <label v-for="category in categoryList" :key="category.id" class="agent-search__checkbox">
                <input type="checkbox" :value="category.id" v-model="categoryIds" /> {{ category.name }}
              </label>
            </div>
          </div>

          <div class="agent-search__dropdown">
            <button type="button" class="agent-btn agent-btn--ghost dropdown-toggle" data-toggle="dropdown">
              Experience ({{ minExperience }}–{{ maxExperience }}y)
            </button>
            <div class="dropdown-menu agent-search__panel agent-search__slider-panel">
              <div class="agent-search__slider" :class="{ 'agent-search__slider--active': isDraggingExperience }">
                <div class="agent-search__slider-track"></div>
                <div class="agent-search__slider-fill" :style="experienceFillStyle"></div>
                <input
                  type="range"
                  min="1"
                  max="25"
                  v-model.number="minExperience"
                  @input="clampMin"
                  @mousedown="isDraggingExperience = true"
                  @touchstart="isDraggingExperience = true"
                  @mouseup="isDraggingExperience = false"
                  @touchend="isDraggingExperience = false"
                />
                <input
                  type="range"
                  min="1"
                  max="25"
                  v-model.number="maxExperience"
                  @input="clampMax"
                  @mousedown="isDraggingExperience = true"
                  @touchstart="isDraggingExperience = true"
                  @mouseup="isDraggingExperience = false"
                  @touchend="isDraggingExperience = false"
                />
              </div>
              <div class="agent-search__slider-labels">
                <span>{{ minExperience }}y</span>
                <span>{{ maxExperience }}y</span>
              </div>
            </div>
          </div>

          <button type="button" class="agent-btn agent-btn--primary agent-search__submit" @click="search">
            Search
          </button>
        </div>
      </div>
    </div>

    <div class="agent-search__results">
      <div v-if="isLoading" class="agent-search__loading">
        <div class="half-circle-spinner">
          <div class="circle circle-1"></div>
          <div class="circle circle-2"></div>
        </div>
      </div>
      <div v-else-if="!data.length" class="agent-search__empty">No agents found matching your search.</div>
      <div v-else class="agent-search__grid">
        <figure class="agent-card" v-for="agent in data" :key="agent.id">
          <img :src="agent.avatar" :alt="agent.name" class="agent-card__photo" />
          <div class="agent-card__body">
            <span class="agent-card__role" v-if="agent.specialties.length">{{ agent.specialties[0] }}</span>
            <span class="agent-card__role" v-else>Real Estate Agent</span>
            <figcaption class="agent-card__name">{{ agent.name }}</figcaption>
            <p class="agent-card__meta" v-if="agent.description">{{ agent.description }}</p>
            <p class="agent-card__meta" v-if="agent.distance !== null">{{ agent.distance }} km away</p>
            <p class="agent-card__meta" v-if="agent.years_of_experience">{{ agent.years_of_experience }} yrs experience</p>
            <p class="agent-card__meta" v-if="agent.languages.length">{{ agent.languages.join(', ') }}</p>
            <div class="agent-card__actions">
              <div class="agent-card__action-wrap" v-if="agent.phone">
                <a
                  class="agent-card__action"
                  :href="'tel:' + agent.phone"
                  @click="onPhoneClick($event, agent)"
                  title="Call agent"
                >
                  <i class="fa fa-phone"></i>
                </a>
                <span v-if="!isMobile && phoneTooltipId === agent.id" class="agent-card__tooltip">{{ agent.phone }}</span>
              </div>
              <a
                v-if="agent.email"
                class="agent-card__action"
                :href="'mailto:' + agent.email"
                title="Email agent"
              >
                <i class="fa fa-envelope"></i>
              </a>
              <a
                v-if="agent.properties_count > 0"
                class="agent-card__listings"
                :href="'/agent-detail/' + agent.username"
              >
                <i class="fa fa-home"></i> {{ agent.properties_count }} listing{{ agent.properties_count === 1 ? '' : 's' }}
              </a>
              <span v-else class="agent-card__listings agent-card__listings--empty">
                <i class="fa fa-home"></i> 0 listings
              </span>
            </div>
          </div>
        </figure>
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
      isDraggingExperience: false,
      isLoading: true,
      data: [],
      links: {},
      isMobile: false,
      phoneTooltipId: null,
      countryList: JSON.parse(this.countries),
      cityList: JSON.parse(this.cities),
      languageList: JSON.parse(this.languages),
      categoryList: JSON.parse(this.categories),
    };
  },
  mounted() {
    this.isMobile = /Android|iPhone|iPad|iPod|IEMobile|Opera Mini/i.test(navigator.userAgent);
    document.addEventListener('click', this.handleOutsideClick);
    this.fetchAgents();
  },
  beforeDestroy() {
    document.removeEventListener('click', this.handleOutsideClick);
  },
  computed: {
    experienceFillStyle() {
      const min = 1;
      const max = 25;
      const leftPct = ((this.minExperience - min) / (max - min)) * 100;
      const rightPct = ((this.maxExperience - min) / (max - min)) * 100;

      return {
        left: leftPct + '%',
        width: Math.max(0, rightPct - leftPct) + '%',
      };
    },
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
    onPhoneClick(event, agent) {
      if (this.isMobile) {
        return;
      }

      event.preventDefault();
      this.phoneTooltipId = this.phoneTooltipId === agent.id ? null : agent.id;
    },
    handleOutsideClick(event) {
      if (this.phoneTooltipId !== null && !event.target.closest('.agent-card__action-wrap')) {
        this.phoneTooltipId = null;
      }
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
.agent-search {
  --agent-navy: #1a1d24;
  --agent-gold: #e0a63e;
  --agent-gold-hover: #cf9530;
  --agent-white: #ffffff;
  --agent-text-muted: #6b7280;
  --agent-label-gray: #9ca3af;
  --agent-border: #e5e7eb;
  --agent-card-bg: #f6f2ea;
  --agent-font-heading: 'Playfair Display', Georgia, serif;
  --agent-font-body: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;

  font-family: var(--agent-font-body);
  max-width: 1320px;
  margin: 0 auto;
  padding: 0 24px;
  position: relative;
}

/* ---- Search card ---- */
.agent-search__card {
  background: var(--agent-white);
  border-radius: 12px;
  box-shadow: 0 24px 60px rgba(0, 0, 0, 0.16);
  padding: 24px;
  margin-top: -48px;
  position: relative;
  z-index: 2;
}

.agent-search__row {
  display: flex;
  align-items: flex-end;
  gap: 16px;
  flex-wrap: wrap;
}

.agent-search__row--primary {
  align-items: stretch;
}

.agent-search__row--location {
  border: 1px solid var(--agent-border);
  border-radius: 8px;
  padding: 6px 16px;
  margin-top: 16px;
  align-items: center;
}

.agent-search__row--filters {
  margin-top: 16px;
  align-items: center;
}

.agent-search__keyword-field {
  flex: 1 1 260px;
}

.agent-search__field {
  flex: 1;
  min-width: 0;
  padding: 8px 0;
}

.agent-search__field-divider {
  width: 1px;
  align-self: stretch;
  background: var(--agent-border);
}

.agent-search__label {
  display: block;
  font-size: 11px;
  font-weight: 600;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  color: var(--agent-label-gray);
  margin-bottom: 4px;
}

.agent-search__input,
.agent-search__select {
  border: none;
  outline: none;
  font-size: 14px;
  color: var(--agent-navy);
  width: 100%;
  background: transparent;
  font-family: var(--agent-font-body);
  padding: 0;
  height: 24px;
}

.agent-search__keyword-field {
  border: 1px solid var(--agent-border);
  border-radius: 8px;
  padding: 8px 16px;
}

/* ---- Buttons ---- */
.agent-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-weight: 600;
  font-size: 14px;
  padding: 0 22px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
  white-space: nowrap;
}

.agent-btn--primary {
  background: var(--agent-gold);
  color: var(--agent-white);
}

.agent-btn--primary:hover {
  background: var(--agent-gold-hover);
}

.agent-btn--outline {
  background: transparent;
  color: var(--agent-navy);
  border: 1px solid var(--agent-border);
}

.agent-btn--outline:hover {
  border-color: var(--agent-gold);
  color: var(--agent-gold-hover);
}

.agent-btn--ghost {
  background: #f4f4f5;
  color: var(--agent-navy);
}

.agent-btn--ghost:hover {
  background: #eceef0;
}

.agent-search__submit {
  margin-left: auto;
  height: 40px;
}

/* ---- Dropdown panels ---- */
.agent-search__dropdown .dropdown-menu {
  border: none;
  box-shadow: 0 16px 40px rgba(26, 29, 36, 0.16);
  border-radius: 8px;
  padding: 16px;
}

.agent-search__panel {
  min-width: 220px;
  max-height: 260px;
  overflow-y: auto;
}

.agent-search__checkbox {
  display: block;
  font-size: 14px;
  color: var(--agent-navy);
  padding: 4px 0;
  cursor: pointer;
}

.agent-search__slider-panel {
  min-width: 260px;
}

.agent-search__slider {
  position: relative;
  height: 30px;
}

.agent-search__slider-track {
  position: absolute;
  top: 17px;
  left: 0;
  right: 0;
  height: 4px;
  border-radius: 2px;
  background: var(--agent-border);
}

.agent-search__slider-fill {
  position: absolute;
  top: 17px;
  height: 4px;
  border-radius: 2px;
  background: var(--agent-gold);
  transition: background 0.15s ease;
}

.agent-search__slider--active .agent-search__slider-fill {
  background: var(--agent-gold-hover);
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
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: var(--agent-gold);
  cursor: pointer;
  transition: background 0.15s ease;
}

.agent-search__slider input[type='range']::-moz-range-thumb {
  pointer-events: auto;
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: var(--agent-gold);
  cursor: pointer;
  border: none;
  transition: background 0.15s ease;
}

.agent-search__slider--active input[type='range']::-webkit-slider-thumb {
  background: var(--agent-gold-hover);
}

.agent-search__slider--active input[type='range']::-moz-range-thumb {
  background: var(--agent-gold-hover);
}

.agent-search__slider-labels {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
  font-size: 13px;
  color: var(--agent-text-muted);
}

/* ---- Results ---- */
.agent-search__results {
  padding: 60px 0 90px;
}

.agent-search__loading,
.agent-search__empty {
  text-align: center;
  padding: 60px 0;
  color: var(--agent-text-muted);
  font-size: 16px;
}

.agent-search__grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}

.agent-card {
  background: var(--agent-card-bg);
  border-radius: 12px;
  overflow: hidden;
  margin: 0;
}

.agent-card__photo {
  display: block;
  width: 100%;
  height: 280px;
  object-fit: cover;
}

.agent-card__body {
  padding: 24px;
}

.agent-card__role {
  display: block;
  color: var(--agent-gold);
  font-weight: 600;
  font-size: 11px;
  letter-spacing: 1px;
  text-transform: uppercase;
  margin-bottom: 8px;
}

.agent-card__name {
  font-family: var(--agent-font-heading);
  font-weight: 700;
  font-size: 22px;
  color: var(--agent-navy);
  margin: 0 0 6px;
}

.agent-card__meta {
  color: var(--agent-text-muted);
  font-size: 14px;
  margin: 0 0 4px;
}

.agent-card__actions {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 16px;
  flex-wrap: wrap;
}

.agent-card__action {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: var(--agent-navy);
  color: var(--agent-white);
  font-size: 14px;
  border: none;
  cursor: pointer;
  transition: background 0.2s ease;
}

.agent-card__action:hover {
  background: var(--agent-gold);
  color: var(--agent-navy);
}

.agent-card__action-wrap {
  position: relative;
  display: inline-flex;
}

.agent-card__tooltip {
  position: absolute;
  bottom: calc(100% + 8px);
  left: 0;
  background: var(--agent-navy);
  color: var(--agent-white);
  font-size: 12px;
  font-weight: 600;
  white-space: nowrap;
  padding: 6px 10px;
  border-radius: 6px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  z-index: 3;
}

.agent-card__tooltip::after {
  content: '';
  position: absolute;
  top: 100%;
  left: 19px;
  transform: translateX(-50%);
  border: 5px solid transparent;
  border-top-color: var(--agent-navy);
}

.agent-card__listings {
  font-size: 13px;
  font-weight: 600;
  color: var(--agent-navy);
  text-decoration: none;
  border-bottom: 2px solid var(--agent-gold);
  padding-bottom: 2px;
}

.agent-card__listings--empty {
  color: var(--agent-text-muted);
  border-bottom-color: transparent;
  cursor: default;
}

/* ---- Responsive ---- */
@media (max-width: 1200px) {
  .agent-search__grid {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 1024px) {
  .agent-search__grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .agent-search {
    padding: 0 16px;
  }

  .agent-search__card {
    margin-top: -32px;
    padding: 16px;
  }

  .agent-search__row {
    flex-direction: column;
    align-items: stretch;
  }

  .agent-search__keyword-field {
    flex: 1 1 auto;
  }

  .agent-search__row--location {
    flex-direction: row;
  }

  .agent-search__field-divider {
    display: none;
  }

  .agent-search__dropdown,
  .agent-search__dropdown .dropdown-menu,
  .agent-search__panel {
    width: 100%;
    min-width: 0;
  }

  .agent-search__submit {
    margin-left: 0;
    width: 100%;
  }

  .agent-search__grid {
    grid-template-columns: 1fr;
  }
}
</style>
