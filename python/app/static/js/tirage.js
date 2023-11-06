var select_n_team = document.getElementById('n_team');
var n_team = 0
// Ajoutez un écouteur d'événement pour détecter les changements de valeur
select_n_team.addEventListener('change', function() {
    var n_team = select_n_team.value;
    display_button(n_team, countChecked)
});


var checkboxes = document.querySelectorAll('input[name="team_select"]');
var countChecked = 0;

function updateCheckedCount() {
    countChecked = 0;

    checkboxes.forEach(function (checkbox) {
        if (checkbox.checked) {
            countChecked++;
        }
    });
    let span_n_team_count = document.querySelector('.n_team_count');
    span_n_team_count.innerHTML = countChecked
    n_team = select_n_team.value;
    display_button(n_team, countChecked)
}

checkboxes.forEach(function (checkbox) {
    checkbox.addEventListener('change', updateCheckedCount);
    
});

function display_button(n_team, countChecked){
    let button = document.querySelector('.button_validate');

    if (parseInt(n_team) === countChecked) {
        button.style.display = "block"
        console.log("yes");
    }
    else{
        button.style.display = "none"
    }
}
