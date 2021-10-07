<template>
  <div>
    <div class="flex justify-center mt-32 mb-8">
      <input type="search" id="address" v-model="citySearch" v-on:keyup="getSearch"
             class="shadow appearance-none border rounded-full text-center w-96 py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                 placeholder="Search a city for weather data"/>
    </div>
    <div class="wrap-bg maxWidth frosted mb-8 ">
      <div class="row justify-center">
        <div class="img"><img v-bind:src="'/images/weather-icons/' + currentTemperature.icon + '.svg'" alt="Weather icon"/></div>
        <div class="data">
          <div class="temp">{{ currentTemperature.actual }}<sup class="degrees">°C</sup></div>
          <div class="desc">{{ currentTemperature.summary }}</div>
        </div>
      </div>
      <div class="city">{{ location.name }}</div>
      <div class="row flex justify-evenly mt-12">
        <div class="item-wrap">
          <div class="item-title">Wind</div>
          <div class="item-data">{{ currentTemperature.windSpeed }}<span>km/h</span></div>
        </div>
        <div class="item-wrap">
          <div class="item-title">Humidity</div>
          <div class="item-data">{{ currentTemperature.humidity }}<span>%</span></div>
        </div>
<!--        <div class="item-wrap">-->
<!--          <div class="item-title">Precipitation</div>-->
<!--          <div class="item-data">25<span>%</span></div>-->
<!--        </div>-->
      </div>
      <div class="row flex flex-col">
        <div class="hourly">Hourly</div>
        <div class="swiper mySwiper">
          <div class="hours-wrap swiper-wrapper">


            <div class="hour-widget swiper-slide" v-for="(day, index) in hourly" :key="day.time" >
              <div class="time">{{ toHour(day.dt) }}</div>
              <div class="icon"><img v-bind:src="'/images/weather-icons/' + day.weather[0].icon + '.svg'" alt="Sunny weather"/></div>
              <div class="temp">{{ toRounded(day.temp) }}<sup>°</sup></div>
            </div>


          </div>
        </div>
      </div>
    </div>



    <div class="wrap-bg maxWidth frosted mb-8">
      <div class="weather-list" v-for="(day, index) in daily" :key="day.time"  v-if="index < 7">
        <div class="day-row" :class="{'border-none' : index > 5}">
          <div class="day-name">{{ toDayOfWeek(day.dt) }}</div>
          <div class="day-data">
            <div class="icon"><img v-bind:src="'/images/weather-icons/' + day.weather[0].icon + '.svg'" alt=""/></div>
            <div class="temp-high">{{ toRounded(day.temp.max) }}<sup>°C</sup></div>
            <div class="temp-low">{{ toRounded(day.temp.min) }}<sup>°C</sup></div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-icons grid-cols-3 gap-8 pb-12">
      <div class="wrap-bg frosted">
        <div class="title">Feels like</div>
        <div class="data">
          <div class="centerIt">
            {{ currentTemperature.feels }}<span>°C</span></div>
        </div>
      </div>
      <div class="wrap-bg frosted" v-if="currentTemperature.hourPastSunrise == false">
        <div class="title">Sunset</div>
        <div class="icon"><img src="images/weather-icons/sunset.svg" alt="Sunset"/></div>
        <div class="value">{{ toHour(sunset) }}</div>
      </div>
      <div class="wrap-bg frosted" v-if="currentTemperature.hourPastSunrise == true">
        <div class="title">Sunrise</div>
        <div class="icon"><img src="images/weather-icons/sunrise.svg" alt="Sunset"/></div>
        <div class="value">{{ toHour(sunrise) }}</div>
      </div>
      <div class="wrap-bg frosted">
        <div class="title">Uv index</div>
        <div class="icon-only"><img v-bind:src="'/images/weather-icons/uv-' + toRounded(currentTemperature.uvi) + '.svg'" alt="Uv index"/></div>
      </div>
      <div class="wrap-bg frosted">
        <div class="title">Pressure</div>
        <div class="icon"><img src="images/weather-icons/barometer.svg" alt="Sunset"/></div>
        <div class="value">{{ currentTemperature.pressure }} hPa</div>
      </div>
      <div class="wrap-bg frosted">
        <div class="title">Wind</div>
        <div class="icon"><img src="images/weather-icons/wind.svg" alt="Sunset"/></div>
        <div class="value">{{ currentTemperature.windSpeed }}km/h - {{ degToCompass(currentTemperature.windDirection) }}</div>
      </div>
      <div class="wrap-bg frosted">
        <div class="title">Visibility</div>
        <div class="icon"><img src="images/weather-icons/visibility.svg" alt="Sunset"/></div>
        <div class="value">{{ toRounded(currentTemperature.visibility / 1000) }}km</div>
      </div>
    </div>


  </div>
</template>

<script>
export default {
  mounted() {
    this.fetchData()
  },
  data() {
    return {
      backgroundImg: '',
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
        windSpeed: '',
        windDirection: '',
        humidity: '',
        sunrise: '',
        sunset: '',
        hourPastSunrise : '',
        pressure: '',
        uvi: '',
      },
      currentIcon : '',
      hourly: {},
      sunrise: '',
      sunset: '',
      daily: {},
      currentHour: '',
      location: {
        name: 'Murska Sobota, Slovenia',
        lat: 46.6625,
        lon: 16.1664,
      },
      lastSearchedCity: {},
      retrievedObject: []
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
            this.location.name = data.items[0].address.city;
            let latitude = data.items[0].position.lat;
            let longtitude = data.items[0].position.lng;
            console.log("This city search: " +this.citySearch)

            var lastSearchedCity = { 'city': this.location.name, 'lat': latitude, 'lon': longtitude };
            localStorage.setItem('lastSearchedCity',  JSON.stringify(lastSearchedCity));


            fetch(`/api/weather?lat=${latitude}&lon=${longtitude}`)
              .then(response => response.json())
              .then(data => {
                console.log(data)
                this.currentTemperature.actual = Math.round(data.current.temp)
                this.currentTemperature.feels = Math.round(data.current.feels_like)
                this.currentTemperature.summary = data.current.weather[0].description
                this.currentTemperature.icon = data.current.weather[0].icon
                this.currentTemperature.windSpeed = Math.round(data.current.wind_speed)
                this.currentTemperature.windDirection = data.current.wind_deg
                this.currentTemperature.humidity = data.current.humidity
                this.currentTemperature.sunrise = new Date(data.current.sunrise * 1000)
                this.currentTemperature.sunset = new Date(data.current.sunset * 1000)

                this.sunrise = data.current.sunrise
                this.sunset = data.current.sunset

                this.daily = data.daily

                this.currentTemperature.visibility = data.current.visibility
                this.currentTemperature.pressure = data.current.pressure
                this.currentTemperature.uvi = data.current.uvi

                this.hourly = data.hourly

                let d = new Date();
                let n = d.getHours() + "." + d.getMinutes();
                this.currentHour = n

                let sunriseHour = this.currentTemperature.sunrise;
                let sunsetHour = this.currentTemperature.sunset;
                let sunriseDecimal = sunriseHour.getHours() + "." + sunriseHour.getMinutes()
                let sunsetDecimal = sunsetHour.getHours() + "." + sunsetHour.getMinutes()

                console.log("Current hour: " + n)
                console.log("Sunrise hour: " + sunriseHour.getHours() + "." + sunriseHour.getMinutes())
                console.log("Sunset hour: " + sunsetHour.getHours() + "." + sunsetHour.getMinutes())



                if (this.currentHour < sunsetDecimal) {
                  console.log("Show sunset")
                  this.currentTemperature.hourPastSunrise = false;
                }
                else if (this.currentHour > sunriseDecimal) {
                  console.log("Show sunrise")
                  this.currentTemperature.hourPastSunrise = true;
                }

                let currentIcon = data.current.weather[0].icon

                const backgrounds = {
                  '01d' : 'clear-day',
                  '01n' : 'clear-night',
                  '02d' : 'partly-cloudy-day',
                  '02n' : 'partly-cloudy-night',
                  '03d' : 'cloudy-day',
                  '03n' : 'cloudy-night',
                  '04d' : 'cloudy-day',
                  '04n' : 'cloudy-night',
                  '09d' : 'rain-day',
                  '09n' : 'rain-night',
                  '10d' : 'partly-cloudy-rain-day',
                  '10n' : 'partly-cloudy-rain-night',
                  '11d' : 'thunderstorm-day',
                  '11n' : 'thunderstorm-night',
                  '13d' : 'partly-cloudy-snow-day',
                  '13n' : 'partly-cloudy-snow-night',
                  '50d' : 'mist-day',
                  '50n' : 'mist-night'
                };

                const backgroundsArr = backgrounds[currentIcon];
                console.log(backgroundsArr);
                //"'/images/weather-icons/' + day.weather[0].icon + '.svg'"
                document.body.style.backgroundImage = "url('images/backgrounds/" + backgroundsArr + ".jpg";



              })

          })
      }
      this.log += e.key;
    },

    fetchData() {

      let lastSearchedCity = localStorage.getItem('lastSearchedCity')

      let lastSearchedCityObj = JSON.parse(lastSearchedCity);
      console.log(lastSearchedCityObj.city, lastSearchedCityObj.lat, lastSearchedCityObj.lon)




      let apiRoute = '';
      if (lastSearchedCity) {
        console.log("Last searched city: " + localStorage.getItem('lastSearchedCity'))
        fetch(`/api/city?cityName=${this.citySearch}`)
        apiRoute = `/api/weather?lat=${lastSearchedCityObj.lat}&lon=${lastSearchedCityObj.lon}`
        this.location.name = lastSearchedCityObj.city
      }
      else {
        console.log("No last searched city")
        fetch(`/api/weather?lat=${this.location.lat}&lon=${this.location.lon}`)
        apiRoute = `/api/weather?lat=${this.location.lat}&lon=${this.location.lon}`
      }

      console.log(apiRoute)

      fetch(apiRoute)
        .then(response => response.json())
        .then(data => {
          console.log(data)
          this.currentTemperature.actual = Math.round(data.current.temp)
          this.currentTemperature.feels = Math.round(data.current.feels_like)
          this.currentTemperature.summary = data.current.weather[0].description
          this.currentTemperature.icon = data.current.weather[0].icon
          this.currentTemperature.windSpeed = Math.round(data.current.wind_speed)
          this.currentTemperature.windDirection = data.current.wind_deg
          this.currentTemperature.humidity = data.current.humidity
          this.currentTemperature.sunrise = new Date(data.current.sunrise * 1000)
          this.currentTemperature.sunset = new Date(data.current.sunset * 1000)
          this.sunrise = data.current.sunrise
          this.sunset = data.current.sunset
          this.daily = data.daily
          this.currentTemperature.visibility = data.current.visibility
          this.currentTemperature.pressure = data.current.pressure
          this.currentTemperature.uvi = data.current.uvi
          this.hourly = data.hourly

          let d = new Date();
          let n = d.getHours() + "." + d.getMinutes();
          this.currentHour = n

          let sunriseHour = this.currentTemperature.sunrise;
          let sunsetHour = this.currentTemperature.sunset;
          let sunriseDecimal = sunriseHour.getHours() + "." + sunriseHour.getMinutes()
          let sunsetDecimal = sunsetHour.getHours() + "." + sunsetHour.getMinutes()

          console.log("Current hour: " + n)
          console.log("Sunrise hour: " + sunriseHour.getHours() + "." + sunriseHour.getMinutes())
          console.log("Sunset hour: " + sunsetHour.getHours() + "." + sunsetHour.getMinutes())

          if(this.currentHour < sunsetDecimal) {
            console.log("Show sunset")
            this.currentTemperature.hourPastSunrise = false;
          } else if(this.currentHour > sunriseDecimal) {
            console.log("Show sunrise")
            this.currentTemperature.hourPastSunrise = true;
          }

          let currentIcon = data.current.weather[0].icon

          const backgrounds = {
            '01d' : 'clear-day',
            '01n' : 'clear-night',
            '02d' : 'partly-cloudy-day',
            '02n' : 'partly-cloudy-night',
            '03d' : 'cloudy-day',
            '03n' : 'cloudy-night',
            '04d' : 'cloudy-day',
            '04n' : 'cloudy-night',
            '09d' : 'rain-day',
            '09n' : 'rain-night',
            '10d' : 'partly-cloudy-rain-day',
            '10n' : 'partly-cloudy-rain-night',
            '11d' : 'thunderstorm-day',
            '11n' : 'thunderstorm-night',
            '13d' : 'partly-cloudy-snow-day',
            '13n' : 'partly-cloudy-snow-night',
            '50d' : 'mist-day',
            '50n' : 'mist-night'
        };

          const backgroundsArr = backgrounds[currentIcon];
          console.log(backgroundsArr);
          //"'/images/weather-icons/' + day.weather[0].icon + '.svg'"
          document.body.style.backgroundImage = "url('images/backgrounds/" + backgroundsArr + ".jpg";


        })
    },
    toDayOfWeek(timestamp) {
      const newDate = new Date(timestamp * 1000)
      const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursdsay', 'Friday', 'Saturday'];
      return days[newDate.getDay()]
    },
    toRounded(number) {
      const roundedNum = Math.round(number)
      return roundedNum;
    },
    toHour(number) {
      const newTime = new Date(number * 1000)
      let hours = newTime.getHours()
      let minutes = newTime.getMinutes()
      minutes = minutes > 9 ? minutes : '0' + minutes
      return hours + ":" + minutes;
    },
    degToCompass(num) {
      const val = Math.floor((num / 45) + 0.5);
      const arr = ["N", "NE", "E", "SE", "S", "SW", "W", "NW"];
      return arr[(val % 8)]
    },
  }
}
</script>
