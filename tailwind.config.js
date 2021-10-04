module.exports = {
  mode: 'production',
  purge: {
    enabled: true,
    content: [
      './resources/**/*.blade.php',
      './resources/**/*.js',
      './resources/**/*.vue',
    ]
  }

  ,
  darkMode: false, // or 'media' or 'class'
  theme   : {
    extend: {
      spacing: {
        '128': '32rem'
      }
    },
  },
  variants: {
    extend: {},
  },
  plugins : [],
}
