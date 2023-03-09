const cadForm = document.querySelector(`#cad-assunto`)

cadForm.addEventListener("submit", async (e) => {
    e.preventDefault()
    
    const dadosForm = new FormData(cadForm)
    dadosForm.append("add", 1)
    
    try {
        const dados = await fetch("cadastrarAssunto.php", {
            method: "POST",
            body: dadosForm,
        })
    } catch (error) {
        console.log(`erro` + error)
    }
    

})