let boardItems = document.getElementsByClassName('boardItem')

function readDoc(tr){
    location.href = "/board/Read.php?bid=" + tr.children[0].innerHTML
}