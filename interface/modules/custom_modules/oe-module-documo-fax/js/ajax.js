
document.getElementById('searchtype').addEventListener("change", numberType);
function numberType() {
    const type = document.getElementById('searchtype').value;
    if (type === 'prefix') {
        document.getElementById("areacode").style.display = 'block';
    }
}
document.getElementById('checkfornumbers').addEventListener("click", numberSearch);
async function numberSearch(e) {
    e.preventDefault();
    document.getElementById('awaiting').style.display = 'block';
    const xhr = new XMLHttpRequest();
    const prefix = document.getElementById('prefix').value;
    const state = document.getElementById('state').value;
    const zip = document.getElementById('zip').value;
    const token = document.getElementById('token').value;
    let output = '<h3>Available Numbers</h3>';
    let data = "areacode="+prefix;
    data += "&state="+state;
    data += "&zip="+zip;
    data += "&token="+token;

    xhr.onload = function() {
        if(this.status === 200) {
            let response = JSON.parse(this.responseText);
            response.rows.forEach(function(row) {
                output += `<tr><td>
                <div class="checkbox card-body" style="font-size: large">
                    <input id="${row.number_e164}" type="checkbox" class="styled" name="${row.number_e164}" value="${row.number_e164}">
                    <label style="padding-left: 10px" for="${row.number_e164}">${row.number_e164}, ${row.regional_data.rate_center}, ${row.regional_data.state}, ${row.regional_data.country_iso}</label>
                </div></td></tr>
                `;
            })
            document.getElementById('awaiting').style.display = 'none';
            document.getElementById('numberdisplay').innerHTML = output;
            document.getElementById('provisionSubmit').style.display = 'block'
        }
    }

    xhr.open('POST', 'provision_helper.php', true);
    xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhr.send(data);
}


