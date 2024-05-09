
var host = process.env.VUE_APP_HOST;

export default function (url, target = '_blank') {
  window.open(`${host}${url}`, target)
}
