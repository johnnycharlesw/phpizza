function setCookie(cname: string, cvalue: string, exdays: number) {
  const d = new Date();
  d.setTime(d.getTime() + (exdays*24*60*60*1000));
  let expires = "expires="+ d.toUTCString();
  document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

window.addEventListener('message', function (params: any) {
    if (params['phpsessid']) {
        setCookie('PHPSESSID', params['phpsessid'], 30);
    }
})