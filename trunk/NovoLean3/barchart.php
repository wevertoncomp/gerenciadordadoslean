    <div class="box">

        <div class="box-chart">

            <canvas id="GraficoBarra" style="width:100%;"></canvas>

            <script type="text/javascript">                                        

                var options = {
                    responsive:true
                };

                var data = {
                    labels: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"],
                    datasets: [
                        {
                            label: "Dados primários",
                            fillColor: "rgba(220,220,220,0.5)",
                            strokeColor: "rgba(220,220,220,0.8)",
                            highlightFill: "rgba(220,220,220,0.75)",
                            highlightStroke: "rgba(220,220,220,1)",
                            data: [randomnb(), randomnb(), randomnb(), randomnb(), randomnb(), randomnb(), randomnb(), randomnb(), randomnb(), randomnb(), randomnb(), randomnb()]
                        },
                        {
                            label: "Dados secundários",
                            fillColor: "rgba(151,187,205,0.5)",
                            strokeColor: "rgba(151,187,205,0.8)",
                            highlightFill: "rgba(151,187,205,0.75)",
                            highlightStroke: "rgba(151,187,205,1)",
                            data: [28, 48, 40, 19, 86, 27, 90, randomnb(), randomnb(), randomnb(), randomnb(), randomnb()]
                        }
                    ]
                };                

                window.onload = function(){
                    var ctx = document.getElementById("GraficoBarra").getContext("2d");
                    var BarChart = new Chart(ctx).Bar(data, options);
                }           
            </script>

        </div>
    
    </div>