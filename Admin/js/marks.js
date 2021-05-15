const form = document.getElementById('add-marks'),
studentInput = form.querySelector('input#student');

// function to change input status
function handleInput (status, message = 'Student Not Found') {
    if(status){
        studentInput.classList.remove('input-alert');
        form.querySelector('small.student').textContent = '';
        form.querySelector('ul#hints').style.display = 'block';
    }else{
        studentInput.classList.add('input-alert');
        form.querySelector('small.student').textContent = message;
        form.querySelector('ul#hints').style.display = 'none';
    }
}

// function to handle visibility of hints list items
function visibility (visibility) {
    if(visibility) {
        form.querySelector('ul#hints').style.display = 'block';
    }else{
        form.querySelector('ul#hints').style.display = 'none';
    }
}

// function to print li value in search input by click
function printValue () {
    var searchItems = form.querySelectorAll("ul#hints li");

    for (var i = 0; i < searchItems.length; i++) {
        searchItems[i].onclick = function () {
            studentInput.value = this.innerHTML;
            studentInput.focus();
            studentInput.blur();
            visibility(false);
        }
    }
}

studentInput.onfocus = function () {
    studentInput.oninput = function () {
        let enteredValue = this.value;
    
        if(enteredValue !== ""){
            const getData = new XMLHttpRequest();
            getData.open('GET', 'ajax/marks.php?value=' + enteredValue);
            getData.onload = function () {
                if(this.readyState === 4 && this.status === 200){
    
                    let jsonData = JSON.parse(this.responseText);
                    if(jsonData !== 0){ // student info found
    
                        form.querySelector('ul#hints').innerHTML = '';
    
                        handleInput(true);
    
                        // looping into json result
                        for(let i = 0; i < jsonData.length; i++){
    
                            let liElemenet = document.createElement("li"),
                                text       = document.createTextNode(jsonData[i]['name']);
                            liElemenet.appendChild(text);
                            form.querySelector('ul#hints').appendChild(liElemenet);
    
                        }
                        printValue();
    
                    }else{ // not found
                        handleInput(false);
                    }
    
                }else{
                    handleInput(false, 'Unexpected Error Has Happened')
                }
            }
            getData.send();
        }else{
            visibility(false);
        }
    
    }
}

studentInput.onblur = function () {
    if(this.value !== ""){ // blur and value is not empty
        var checkAvilability = new XMLHttpRequest();
        checkAvilability.open("GET", "ajax/marks.php?check_name=" + studentInput.value);
        checkAvilability.onload = function () {
            if(this.readyState === 4 && this .status === 200){
                if(this.response != 0){

                    var data = JSON.parse(this.response);

                    document.getElementById("student-id").value = data['id'];

                }else{
                    document.getElementById("student-id").value = "NULL";
                    handleInput(false);
                }
            }else{
                handleInput(false, "Unexpected Error Has Happened");
            }
        }
        checkAvilability.send();
    }
}