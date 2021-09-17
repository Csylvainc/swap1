$(function() {
    
    // $("#prixSelect").change(filtre());
    // $("#categorie").change(filtre());
    
       
       
    

function filtre(){
    let prix=$("#prixSelect").val();
    let categorie=$("#categorie").val();
    alert(categorie);
    
    $.ajax({
        url: "json.php?prix="+prix+"&categorie="+categorie, // la page du serveur à interroger
        type : "get", // par defaut GET
        dataType : "json"
    }).done(function(data){ // permet de récupérer les réponses positive du serveur
            // dans l'argument data je vais récupérer les données renvoyées par le serveur
            alert(data);

    }).fail(function(){ // permet de récupérer la réponse négative du serveur
            alert('fail');
    });         
}
filtre();

  });