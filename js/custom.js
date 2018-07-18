$(document).ready(function () {

      // Painel de adição de contato ao ponto
      $('#modal-delete-contact').on('show.bs.modal', function(e) {                
        $('#modal-ctc-id').val(e.relatedTarget.id);   
      });
      // /- Painel de adição de contato ao ponto
      

	// Serviço de busca de CEP
      function limpa_formulário_cep() {
          $("#rua").val("");
          $("#bairro").val("");
          $("#cidade").val("");
          $("#uf").val("");          
      }

      $("#cep").blur(function() {
      	var cep = $(this).val().replace(/\D/g, '');
      	if (cep != "") {
          var validacep = /^[0-9]{8}$/;
          if(validacep.test(cep)) {
            
            $("#rua").val("...");
            $("#bairro").val("...");
            $("#cidade").val("...");
            $("#uf").val("...");
            $("#ibge").val("...");

            $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {
              if (!("erro" in dados)) {
                  //Atualiza os campos com os valores da consulta.
                  $("#rua").val(dados.logradouro);
                  $("#bairro").val(dados.bairro);
                  $("#cidade").val(dados.localidade);
                  $("#uf").val(dados.uf);                  
              } //end if.
              else {
                  //CEP pesquisado não foi encontrado.
                  limpa_formulário_cep();
                  alert("CEP não encontrado.");
              }
            });

          } else {
            limpa_formulário_cep();
            alert("Formato de CEP inválido.");
          }
        } else {
          limpa_formulário_cep();
        }
      });
})
