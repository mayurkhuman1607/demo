  function submitForm() {
    var form = document.getElementById("myform");
    var xhr = new XMLHttpRequest();
    xhr.open('POST', form.action, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
      if (xhr.readyState === 4 && xhr.status === 200) {
        var response = JSON.parse(xhr.responseText);
        document.getElementById("response").innerHTML = response;
      }
    }
    var formData = new FormData(form);
    xhr.send(formData);
  }