@extends('layouts/contentNavbarLayout')

@section('title', 'Log Reader')

@section('content')
<h4 class="py-3 mb-4">Log Reader
</h4>

<div class="card">
  <h5 class="card-header d-flex justify-content-between align-items-center">
    Arquivos
    
    <span style="font-size: 0.73em" class="counterString text-muted"></span>
    
  </h5>
  <div class="table-responsive text-nowrap">
    <table class="table" id="logFilesTable">
      <thead class="table-light">
        <tr>
          <th>Batch Id</th>
          <th>Data Envio</th>
          <th class="text-center">Status</th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
        @if(!empty($logFiles) && count($logFiles) > 0)

          @foreach($logFiles as $logFile)
            <tr>
              <td>{{$logFile->batch_id}}</td>
              <td>{{$logFile->created_at}}</td>
              <td class="text-center">
                <span class="badge rounded-pill me-1 bg-label-{{ $logFile->status == 'processing' ? 'warning' : ($logFile->status == 'finished' ? 'success' : 'danger') }}">
                  @if($logFile->status == 'processing')
                    Processando
                  @elseif($logFile->status == 'finished')
                    Finalizado
                  @else
                    Erro
                  @endif
                </span>
              </td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="3" class="text-center">Nenhum arquivo encontrado</td>
          </tr>
        @endif
        <tr>
          <form action="{{route('logReader.uploadLogFile')}}" method="post" enctype="multipart/form-data" accept="text/plain">
            @csrf
            <td colspan="3">
              <div class="d-flex flex-row justify-content-start align-items-end">
                <div>
                  <label for="defaultFormControlInput" class="form-label">Enviar arquivo (.txt)</label>
                  <input type="file" name="log_file" id="log_file" required class="form-control">
                </div>
                <button type="submit" class="btn btn-outline-primary ms-2">Enviar</button>
              </div>
            </td>
          </form>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<hr class="my-5">

<div class="card">
  <h5 class="card-header">Relatórios</h5>
  <div class="table-responsive text-nowrap">
    <table class="table">
      <thead class="table-light">
        <tr>
          <th>Relatório</th>
          <th class="text-center" style="width: 250px">
            Ações
          </th>
        </tr>
      </thead>
      <tbody class="table-border-bottom-0">
   
            <tr>
              <td>
                Requisições por consumidor
              </td>
              <td class="text-center">
                <button class="btn btn-outline-primary rounded-pill btn-sm" onclick="downloadReport(this, 'requests_by_consumer')">
                  Baixar
                </button>
              </td>
            </tr>

            <tr>
              <td>
                Requisições por serviço
              </td>
              <td class="text-center">
                <button class="btn btn-outline-primary rounded-pill btn-sm" onclick="downloadReport(this, 'requests_by_service')">
                  Baixar
                </button>
              </td>
            </tr>

            <tr>
              <td>
                Tempo médio de request, proxy e gateway por serviço.
              </td>
              <td class="text-center">
                <button class="btn btn-outline-primary rounded-pill btn-sm" onclick="downloadReport(this, 'average_times_by_service')">
                  Baixar
                </button>
              </td>
            </tr>
       
      </tbody>
    </table>
  </div>
</div>

<script>
  function updateStatus(){
      $.ajax({
          url: "{{route('logReader.files')}}",
          type: 'GET',
          success: function(data){
              data.forEach(logFile => {
                  // Encontrar a linha que contém o batch_id
                  let rows = document.querySelectorAll('#logFilesTable tr');
                  let targetRow;
                  rows.forEach(row => {
                      let cells = row.querySelectorAll('td');
                      cells.forEach(cell => {
                          if (cell.textContent.includes(logFile.batch_id)) {
                              targetRow = row;
                          }
                      });
                  });

                  // Atualizar a célula de status na linha encontrada
                  if (targetRow) {
                      let statusCell = targetRow.querySelector('td:last-child');
                      statusCell.innerHTML = `<span class="badge rounded-pill me-1 bg-label-${logFile.status == 'processing' ? 'warning' : (logFile.status == 'finished' ? 'success' : 'danger')}">
                          ${logFile.status == 'processing' ? 'Processando' : (logFile.status == 'finished' ? 'Finalizado' : 'Erro')}
                      </span>`;
                  }
              });
          }
      });
  }

  function downloadReport(button, reportType) {
    $(button).html(`<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Baixando...`);
    $(button).prop('disabled', true);

    fetch("{{route('report.generateReport')}}?reportType=" + reportType)
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `relatorio_${reportType}.csv`;
            a.click();

            $(button).html('Baixar');
            $(button).prop('disabled', false);
        })
        .catch(error => {
            alert('Erro ao baixar o relatório');
            console.error(error); // Adicione esta linha para depurar erros
        });
  }

  let countTillUpdate = 10;

  setInterval(() => {
    countTillUpdate--;
    if(countTillUpdate == 0){
      $('.counterString').text('Atualizando...');
      updateStatus();
      countTillUpdate = 10;
    }else{
      $('.counterString').text('Atualizando em ' + countTillUpdate + ' segundos');
    }
  }, 1000);

</script>

@endsection
