# php_samples


$_ENV 
$_GET
$_POST
$_COOKIE
$_SERVER
$_FILE

$_SERVER['REQUEST_METHOD']

isset($_GET['index']) //controlla se è settato

$_FILE['immagine'] 
  name
  tmp_name
  type
  size
  error

move_uploaded_file($_FILE['IMMAGINE']['tmp_name'],$dest_folder)

strip_tags(<string>) serve per la sicurezza,ritorna una stringa senza tag html !importante

SESSIONI

id univoco
raccolta di variabili
session_start() -> va fatta prima di qualsiasi output di php -> prima dell head
se una sessione non esiste ne crea una,oppure recupera quella esistente

l'id proviene da un cookie

$_SESSION disponibile dopo aver chiamato session_start()
$_SESSION['time']=time(); serve per sapere quanto tempo è passato da quando l'utente ha                             fatto l'ultima richiesta a quella pagina

esempio
ogni volta che carico una pagina aumento il contatore

<?php
session_start();
if(issset($_SESSION['count'])){
  $i=$_SESSION['count']
}else{
  $i=0
}
?>
... verso la fine
<?php
  echo $i
  $_SESSION['count]=$i+1
>


SCADENZA SESSIONE
copiare paro paro dalle slide sessions.php e usarlo con l'include in ogni pagina a cui serve

ACCESSO AL DB