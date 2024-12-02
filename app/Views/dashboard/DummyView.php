
<style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2ecc71;
            --accent-color: #e74c3c;
            --background-color: #ecf0f1;
            --card-color: #ffffff;
            --text-color: #34495e;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .countcontainer{
            display:flex;
            flex-direction : column;
        }
        .cls-votesuccess
        {
            /* width: 60%; */
            margin: 0px auto;
            /* border: 1px solid red;*/
        }
  
        .cls-votesuccess h1 {
                   /* position: absolute; */
            text-align: center;
            color: rgb(58, 109, 0);
            margin-bottom: 1rem;
            /* inset: 30% 0 0 0; */
            font-size: 20px;
            line-height: 30px;
            font-weight:normal;
            }
        .cls-votesuccess img{
            margin-top: 20%;
            margin-left : 40%;
        }
        .cls-votesuccess canvas {
            /* overflow-y: hidden; */
            /* overflow-x: hidden; */
            width: 100%;
            margin: 0;
            position: fixed;
            inset: 0 0 0 0;
        }
        .poker-container {
            padding: 2rem;
            flex-grow: 1;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .user-name {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color); 
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .game-info, .vote-panel {
            background-color: var(--card-color);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .selected-value {
            font-size: 1rem;
            font-weight: 500;
        }

        .received-messages {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1rem;
            margin-top: 1rem;
        }

        .received-message-card {
            background-color: var(--primary-color);
            color: var(--card-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .received-message-card:hover {
            transform: translateY(-5px);
        }

        .vote-panel {
            height: fit-content;
        }

        .status {
            font-weight: 500;
        }

        .average {
            font-weight: 500;
        }

        .actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .action-btn.reveal {
            background-color: var(--PRIMARYCOLOR);
            color: var(--card-color);
            z-index: 1000;      
          }

        .action-btn.restart {
            background-color: var(--PRIMARYCOLOR);
            color: var(--card-color);
            z-index: 1000; 
        }

        .action-btn.exit {
            background-color: var(--PRIMARYCOLOR);
            color: var(--card-color);
            z-index: 1000; 
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 14px rgba(0, 0, 0, 0.1);
        }

        .countdown {
            font-size: 3rem;
            font-weight: 700;
            text-align: center;
            color: var(--primary-color);
        }

        .card-table {
            /* background: linear-gradient(135deg, var(--secondary-color), var(--primary-color)); */
    /* border-radius: 20px; */
    padding: 2rem;
    border: none;
    margin-bottom: 2rem;
    /* box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2); */
    transition: all 0.5s ease;
        }

        .card-selection {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
        }

        .card {
            width: 80px;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--card-color);
            border-radius: 10px;
            border: 9px solid var(--PRIMARYCOLOR);
            cursor: pointer;
            transition: all 1s ease;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-color);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
            /* opacity: 0; */
            transform: translateY(20px);
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, #f5f5f5 25%, transparent 25%) -10px 0,
                        linear-gradient(225deg, #f5f5f5 25%, transparent 25%) -10px 0,
                        linear-gradient(315deg, #f5f5f5 25%, transparent 25%),
                        linear-gradient(45deg, #f5f5f5 25%, transparent 25%);
            background-size: 20px 20px;
            opacity: 0.1;
        }

        .card span {
            position: relative;
            z-index: 1;
        }

        .card:hover {
            transform: translateY(-15%) rotate(5deg) !important ;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
            transition: all 1s ease !important;
            
        }

        .card.active {
            background-color: var(--primary-color);
            color: var(--card-color);
        }
        .vote-hint {
            display: flex;
    justify-content: center;
    align-items: center;
    color: var(--text-color);
    font-size: 1rem;
        }
        .poker-container{
            display: flex;
    justify-content: center;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hidden {
            display: none;
        }
        .voteing-hint { font-size:50px; 
            color: rgb(58, 109, 0);
            margin-left: 15%;}
    </style>
    <div class="poker-container">
        <div class="header">
            <h1 class="user-name" style="display:none;" id="selected-user-name"><?= session()->get("first_name")?></h1>
        </div>
<!--  <div class="game-info">
</div> -->
<div class="poker-container">

       
        <div class="pokercardcontainer">
        <div id="celebrationcontainer"></div>
        <div id="received-messages" class="received-messages"></div>

        <div class="actions">
            <button class="action-btn reveal" style="display: none;">Reveal</button>
            <button class="action-btn restart">Restart</button>
            <button class="action-btn exit">Exit</button>
        </div> 
        <div class="countcontainer actions">
        <div id="countdown" class="countdown"></div>

        </div>
        <div class="vote-hint" id="vote-hint">Click on a card to vote</div>
        <div id="card-table" class="card-table">
            <div class="card-selection" data-series="<?= $data['poker'] ?>">

            </div>

        </div>
        </div>
        </div>
        <div class="vote-panel">
            <span id="vote-count">Votes Submitted: 0</span>
            <div id="selected-value" class="selected-value">Selected: 0</div>
            <span id="message" class="average">Average: 0</span>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    <script>
    // const cardSelection = document.querySelector('.card-selection');
    // const dataSeriesValue = cardSelection.getAttribute('data-series');
    // console.log(dataSeriesValue);
    // if (cardSelection) {
    //     const cards = cardSelection.querySelectorAll('.card');
    //     cards.forEach((card,dataSeriesValue) => {
        // if()
    //             card.style.display = 'none';
    //     });
    // }
    function generateFibonacciSeries(limit)
    {
        let first = 0;
        let second = 1;
        let third;
        let fibarr = [first];

        for (let i = 2; i < limit; i++) {
            third = first + second; 
            fibarr.push(third); 
            first = second;   
            second = third;
        }
        return fibarr;
    }
//generateFibonacciSeries(dataSeriesValue);
function createCards() 
{
        const cardSelection = document.querySelector('.card-selection');
        console.log(cardSelection,'cardSelection');
        const limit = parseInt(cardSelection.getAttribute('data-series'));
        const fibonacciSeries = generateFibonacciSeries(limit);
        console.log(fibonacciSeries,'fibonacciSeries');
        cardSelection.innerHTML = '';  
        // <div class="card" data-value=""><span></span></div>
        fibonacciSeries.forEach(value => {
            if( value <= limit){
            const card = document.createElement('div');
            card.className = 'card';
            card.setAttribute('data-value', value);

            const span = document.createElement('span');
            span.textContent = value;

            card.appendChild(span);
            cardSelection.appendChild(card);
            }
        });     
}

    createCards();

    
    
        // WebSocket connection
        var conn = new WebSocket('ws://localhost:8083/chat');

        const userName = "<?= session()->get('first_name')?>";
        conn.addEventListener('open', function(e) {
            console.log("Connection established");
            conn.send(JSON.stringify({
                action: 'join',
                user: userName
            }));
        });

        var messageCardsMap = new Map();
        var selectedValues = [];

        conn.onmessage = function(e) {
            var data = JSON.parse(e.data);
            console.log(data);
            if (data.action === 'updateVoteCount') {
                document.getElementById('vote-count').textContent = 'Votes Submitted: ' + data.numberofvotes;
            } else if (data.action === 'restart') {
                handleRestart();
            } else if (data.action === 'reveal') {
                handleReveal(data);
            } else if (data.action === 'exit') {
                window.location.href = "<?= ASSERT_PATH ?>.dashboard/dashboardView";
            }
        };

        function handleRestart() {
            document.getElementById('received-messages').innerHTML = '';
            selectedValues = [];
            updateAverage(0);
            document.getElementById('vote-count').textContent = 'Votes Submitted: 0';
            document.querySelectorAll('.card').forEach(function(card) {
                card.classList.remove('active');
            });
            document.getElementById('selected-value').textContent = 'Selected: ';
            document.querySelector('.vote-hint').textContent = "Click on a card to vote";
            document.querySelector('.action-btn.reveal').style.display = 'none';
            document.getElementById('card-table').classList.remove('hidden');
        }

        function handleReveal(data) {
            document.querySelector('.vote-hint').textContent = "Voting Completed";
            document.getElementById('card-table').classList.add('hidden');
            startCountdown(function() {
                // var celebrationcontainer = document.getElementById('celebrationcontainer');
                // celebrationcontainer.innerHTML = ' <div class="cls-votesuccess"><h1>Hey, Thanks for your vote. We get back to you shortly.</h1><canvas id="canvas"></canvas></div>';
                var messagesContainer = document.getElementById('received-messages');
                messagesContainer.innerHTML = '';
                Object.keys(data.votes).forEach(function(user) {
                    var value = data.votes[user];
                    var messageCard = document.createElement('div');
                    messageCard.classList.add('received-message-card');
                    messageCard.innerHTML = '<strong>' + user + ':</strong> ' + value;
                    messagesContainer.appendChild(messageCard);

                });
                updateAverage(data.average);
                confetti
                confetti({
                    particleCount: 200,
                    spread: 70,
                    origin: {
                        y: 0.6
                    }
                });
                var celebrationcontainer = document.getElementById('received-messages');
                document.getElementById("vote-hint").style.display = "none";
        celebrationcontainer.innerHTML = '<div class="cls-votesuccess"><img src="<?= ASSERT_PATH ?>/assets/icon/317-3179395_best-quality-complete-icon-removebg-preview.png" alt="" srcset="" height="100px"> <div class="voteing-hint">Voting completed</div><h1>Hey, Thanks for your vote. We will get back to you shortly.</h1><canvas id="canvased"></canvas></div>';
        var canvas = document.getElementById("canvased");
                var ctx = canvas.getContext("2d");
                var W = window.innerWidth;
                var H = window.innerHeight;
                canvas.width = W;
                canvas.height = H;
            
                var mp = 1000; //max particles
                var particles = [];
                for (var i = 0; i < mp; i++) {
                particles.push({
                    x: Math.random() * W, //x-coordinate
                    y: Math.random() * H, //y-coordinate
                    r: Math.random() * 18 + 1, //radius
                    d: Math.random() * mp, //density
                    color: "rgba(" + Math.floor((Math.random() * 255)) + ", " + Math.floor((Math.random() * 255)) + ", " + Math.floor((Math.random() * 255)) + ", 0.8)",
                    tilt: Math.floor(Math.random() * 5) - 5
                });
                }
            
                //Lets draw the flakes
                function draw() {
                ctx.clearRect(0, 0, W, H);
                for (var i = 0; i < mp; i++) {
                    var p = particles[i];
                    ctx.beginPath();
                    ctx.lineWidth = p.r;
                    ctx.strokeStyle = p.color; // Green path
                    ctx.moveTo(p.x, p.y);
                    ctx.lineTo(p.x + p.tilt + p.r / 2, p.y + p.tilt);
                    ctx.stroke(); // Draw it
                }
            
                update();
                }
            
                var angle = 0;
            
                function update() {
                angle += 0.01;
                for (var i = 0; i < mp; i++) {
                    var p = particles[i];
                    p.y += Math.cos(angle + p.d) + 1 + p.r / 2;
                    p.x += Math.sin(angle) * 2;
                    if (p.x > W + 5 || p.x < -5 || p.y > H) {
                    if (i % 3 > 0) //66.67% of the flakes
                    {
                        particles[i] = {
                        x: Math.random() * W,
                        y: -10,
                        r: p.r,
                        d: p.d,
                        color: p.color,
                        tilt: p.tilt
                        };
                    }
                    }
                }
                }
                setInterval(draw, 20);
            });
        }

        function startCountdown(callback) {
            var countdownElement = document.getElementById('countdown');
            var count = 3;
            countdownElement.textContent = count;
            var interval = setInterval(function() {
                count--;
                if (count > 0) {
                    countdownElement.textContent = count;
                } else {
                    clearInterval(interval);
                    countdownElement.textContent = '';
                    callback();
                }
            }, 1000);
        }

        function updateAverage(average = null) {
            if (average === null) {
                if (selectedValues.length === 0) {
                    document.getElementById('message').textContent = 'Average: 0';
                    return;
                }
                var sum = selectedValues.reduce((a, b) => a + b, 0);
                average = sum / selectedValues.length;
            }
            document.getElementById('message').textContent = 'Average: ' + average.toFixed(2);
        }

        function sendMessage(message) {
            if (message.trim() !== '') {
                conn.send(message);
            }
        }

        document.querySelectorAll('.card').forEach(function(card, index) {
            setTimeout(function() {
                card.style.animation = 'fadeInUp 0.1s ease forwards';
            }, index * 50);

            card.addEventListener('click', function() {
                document.querySelectorAll('.card').forEach(function(c) {
                    c.classList.remove('active');
                });
                this.classList.add('active');
                var selectedValue = parseInt(this.getAttribute('data-value') || 0);
                var selectedUserName = document.getElementById('selected-user-name').textContent;
                document.getElementById('selected-value').textContent = 'Selected: ' + selectedValue;
                sendMessage(JSON.stringify({ action: 'updateVotes', user: selectedUserName, value: selectedValue }));
                document.querySelector('.vote-hint').textContent = "Voting In Progress... Please wait while others vote.";
                document.querySelector('.action-btn.reveal').style.display = 'inline-block';
            });
        });

        document.querySelector('.action-btn.reveal').addEventListener('click', function() {
            sendMessage(JSON.stringify({ action: 'reveal' }));
            this.style.display = 'none';
        });

        document.querySelector('.action-btn.restart').addEventListener('click', function() {
            handleRestart();
            sendMessage(JSON.stringify({ action: 'restart' }));
        });

        document.querySelector('.action-btn.exit').addEventListener('click', function() {
            sendMessage(JSON.stringify({ action: 'exit' }));
        });

      
    </script>
<!-- background-color: #ffffff;
    color: var(--card-color);
    color: black;
    border-radius: 1px;
    box-shadow: 0 0 0 2px rgb(16 32 78), 7px 7px 0 0 rgb(27 41 68); -->