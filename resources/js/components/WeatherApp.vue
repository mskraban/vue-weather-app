<template>
  <div class="text-white mb-8">
      <input type="search" id="address" v-model="citySearch" v-on:keyup="getSearch"  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Search a city for weather data" />


    <div class="weather-container font-sans sm:w-32 md:w-128 max-w-lg overflow-hidden bg-gray-900 shadow-lg mt-4 p-5 pt-0 rounded-lg">
      <div class="current-weather flex items-center justify-between px-6 py-8">
        <div class="flex items-center">
          <div>
            <div class="text-4xl md:text-6xl font-semibold">{{ currentTemperature.actual }}°C</div>
            <div class="text-xs md:text-base">Feels like {{ currentTemperature.feels }}°C</div>
          </div>
          <div class="mx-5">
            <div class="font-semibold summary">{{ currentTemperature.summary }}</div>
            <div class="text-xs md:text-base">{{ location.name }}</div>
          </div>
        </div>
        <div>
          <img v-bind:src="'/images/weather-icons/' + currentTemperature.icon + '.svg'" height="90" width="90" alt="">
        </div>
     </div><!--end current weather-->

      <div class="future-weather text-sm bg-gray-800 px-6  overflow-hidden">

        <div v-for="(day, index) in daily"
             :key="day.time"
             class="flex items-center py-5"
             :class="{'border-b-2 border-gray-900' : index < 4}"
             v-if="index < 5"
        >
          <div class="w-1/6 text-lg text-gray-200">{{ toDayOfWeek(day.dt) }}</div>
          <div class="w-4/6  px-4 flex items-center">
            <div><img v-bind:src="'/images/weather-icons/' + day.weather[0].icon + '.svg'" height="50" width="50" alt=""></div>
            <div class="ml-3 summary">{{ day.weather[0].description }}</div>
          </div>
          <div class="w-1/6 text-right">
            <div>{{ toRounded(day.temp.max) }}°C</div>
            <div>{{ toRounded(day.temp.min) }}°C</div>
          </div>
        </div>

      </div>

    </div><!--end weather container-->
  </div>
</template>

<script>
export default {
  mounted() {
    this.fetchData()
  },
  data() {
    return {
      citySearch: '',
      searchResult: {
        lat: '',
        lon: ''
      },
      currentTemperature: {
        actual: '',
        feels: '',
        summary: '',
        icon: '',
      },
      daily: {},
      location: {
        name: 'Murska Sobota, Slovenia',
        lat: 46.6625,
        lon: 16.1664,
      }
    }
  },
  methods: {
    getSearch: function (e) {
      if (e.keyCode === 13) {
        console.log(this.citySearch);
        fetch(`/api/city?cityName=${this.citySearch}`)
          .then(response => response.json())
          .then(data => {
            console.log(data)
            this.searchResult = data

            this.location.name = data.items[0].address.label;
            let latitude = data.items[0].position.lat;
            let longtitude = data.items[0].position.lng;


              fetch(`/api/weather?lat=${latitude}&lon=${longtitude}`)
                .then(response => response.json())
                .then(data => {
                  console.log(data)
                  this.currentTemperature.actual = Math.round(data.current.temp)
                  this.currentTemperature.feels = Math.round(data.current.feels_like)
                  this.currentTemperature.summary = data.current.weather[0].description
                  this.currentTemperature.icon = data.current.weather[0].icon
                  this.daily = data.daily
                })

          })
      }
      this.log += e.key;

    },
    fetchData() {
      fetch(`/api/weather?lat=${this.location.lat}&lon=${this.location.lon}`)
      .then(response => response.json())
      .then(data => {
        console.log(data)
        this.currentTemperature.actual = Math.round(data.current.temp)
        this.currentTemperature.feels = Math.round(data.current.feels_like)
        this.currentTemperature.summary = data.current.weather[0].description
        this.currentTemperature.icon = data.current.weather[0].icon
        this.daily = data.daily
      })
    },
    toDayOfWeek(timestamp) {
       const newDate = new Date(timestamp*1000)
       const days = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
       return days[newDate.getDay()]
    },
    toRounded(number) {
      const roundedNum = Math.round(number)
      return roundedNum;
    },

  }
}
</script>
