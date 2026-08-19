function getReferenceName(v){
    let value,json,
        xhr = new XMLHttpRequest();

    xhr.open("GET", location.origin+"/get/reference/name?id="+v, false); // true for asynchronous
    xhr.onload = function() {
        if (xhr.status >= 200 && xhr.status < 300) {
            json = JSON.parse(xhr.responseText);
            console.log(json.name);
            value = json.name;
        } else {
            // Request failed
            console.error('Request failed with status:', xhr.status);
        }
    };
    xhr.onerror = function() {
        console.error('Network error occurred.');
    };
    xhr.send();

    return value;
}