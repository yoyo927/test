
## 圖表改進
### 在chart.blade.php編輯
先將下面script的位置改到第一個圖表的canvas下面
```
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.css"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css"></script>
```

在原本圖表的下方新增一個折線圖的code，注意div的位置
```
<div class="content">
        <div class="title m-b-md">
            折線圖
        </div>
        <div>
            <canvas id="myChart_1" width="600" height="600"></canvas>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
            const ctx_1 = document.getElementById("myChart_1").getContext("2d");
            const myChart_1 = new Chart(ctx_1, {
                type: "line",
                data: {
                    labels: ['一月份', '二月份', '三月份','四月份', '五月份', '六月份', '七月份'],
                    datasets: 
                            [
                                {   
                                label: "平均氣溫",
                                data: [19, 21, 23, 26, 28, 29, 30],
                                fill: false,
                                borderColor: 'rgb(54, 162, 235)', // 線的顏色
                                backgroundColor: ['rgba(255, 99, 132, 0.5)'],// 點的顏色
                                pointStyle: 'circle',     //點類型為圓點
                                pointRadius: 6,    //圓點半徑
                                pointHoverRadius: 10, //滑鼠動上去後圓點半徑
                                tension: 0.1
                                }
                                
                            ]
                },
                options: {
                    responsive: true,  // 設置圖表為響應式

                    interaction: {  
                                    intersect: false,
                                },
                    scales: {  
                    x: {
                        display: true,
                        title: {
                                display: true,
                                text: '月份'
                                }
                        },
                    y: {
                        display: true,
                        title: {
                                display: true,
                                text: '氣溫'
                                }
                        }
                    }

                }
            });
            </script>
        </div>
</div>
```
更新網頁會發現圖表位置跑掉，接下來要去改更上層div使用到的class
```
.flex-center {
    align-items: center;
    display: flex;
    flex-direction: column;
    /* justify-content: center; */
}
.position-ref {
    position: relative;
}
```
完成後會長這樣

![image](https://github.com/yoyo927/test/blob/6440f383e11e5742911c0eb6d8cfa961c050d733/lineChart.png)
### 改進第一次做的柱狀圖
- #### 第一步讓圖固定顯示10筆資料
要補上初始化的十筆data

```
const initialData = {
labels: ['2023-10-06 00:05:49', '2023-10-06 00:05:50', '2023-10-06 00:05:51','2023-10-06 00:05:52','2023-10-06 00:05:53','2023-10-06 00:05:54','2023-10-06 00:05:55','2023-10-06 00:05:56','2023-10-06 00:05:57','2023-10-06 00:05:58'],
data: [10, 20, 30, 40, 50, 60, 70, 80, 90, 100],};
```
這邊也要改動
```
data: {
        labels: initialData.labels,
        datasets: [{
            label: '圖表',
            data: initialData.data,
            borderWidth: 1
        }]
    },
```
這邊加上if來做判斷
```
    let evtSource = new EventSource("/chartEventStream", {withCredentials: true});
        evtSource.onmessage = function (e) {
            let serverData = JSON.parse(e.data);
            console.log('EventData:- ', serverData);


        myChart.data.labels.push(serverData.time);
        myChart.data.datasets[0].data.push(serverData.value);

        // 移除舊的數據，保持一個固定的數據點數目
        if (myChart.data.labels.length > 10) {
            myChart.data.labels.shift();
            myChart.data.datasets[0].data.shift();
        }

        myChart.update();
        
    };
```
完成後就能限制圖表資料筆數
這時候如果發現資料顯示的時間有誤，要到config\app.php改時區
```
'timezone' => 'Asia/Taipei',
```

- #### 寫入亂數到資料庫再讀取到圖表
  將HomeController的chartEventStream()函數改寫
  ![image](https://github.com/yoyo927/test/blob/f77c2c11284db72326cbbff0627db10ccbeb31bb/%E8%AE%80%E5%8F%96%E5%AF%AB%E5%85%A5.png)
- #### 制定排程來清除資料表的值
輸入指令創建一個執行動作的php檔
```
  php artisan make:command DeleteChartRecords
```
會在專案名稱\app\Console\Commands\下找，然後更改一下程式

 ![image](https://github.com/yoyo927/test/blob/346a75bc7b1e104bd94fee3b9ffd222d63f481df/command.png)

 再到app\Console\Kernel.php去做排程設定
 
 ![image](https://github.com/yoyo927/test/blob/ee6803c7381f0b196d6e8f5d131587a13b4999e3/command2.png)
 
可以手動測試一下能不能執行，成功的話資料表會清空
```
 php artisan DeleteChartRecords:name
 php artisan schedule:run 執行一次測試能不能順利刪除
 php artisan schedule:list //可以查看工作排程跟下次執行的時間 * 如果有錯誤也沒關係
```
因為在windows所以要再另外設定排程
打開搜尋->工作排程器 打開後點建立工作命名它
設定要執行的時間跟頻率

![image](https://github.com/yoyo927/test/blob/8ba46f79f16fec630f904b7cab5139a48a9d9990/%E6%8E%92%E7%A8%8B1.png)

設定要執行的動作和指令

![image](https://github.com/yoyo927/test/blob/8ba46f79f16fec630f904b7cab5139a48a9d9990/%E6%8E%92%E7%A8%8B2.png)

程式放上php的.exe完整路徑
引數要放上專案artisan的路徑跟指令
```
C:\Users\wonengjinma\blog\artisan schedule:run
```
設定成功後每次執行cmd會跳出來再關掉
