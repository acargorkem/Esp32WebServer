$(document).ready(function() {
    if (typeof data != 'undefined') {
        /* converting string fields to number and date format */
        data.forEach(d => {
            if (d.hasOwnProperty('temperature')) {
                d.temperature = Number(d.temperature);
            }
            if (d.hasOwnProperty('pressure')) {
                d.pressure = Number(d.pressure);
            }
            if (d.hasOwnProperty('altitude')) {
                d.altitude = Number(d.altitude);
            }
            if (d.hasOwnProperty('humidity')) {
                d.humidity = Number(d.humidity);
            }
            d.timestamp = new Date(d.timestamp);
        });

        console.log(data);

        // global start flag
        var graphStarted = false;

        // adding button
        addButtons(data);

        function addGraph(currentDataKey) {

            var svg = d3.select("svg");
            svg.selectAll("*").remove();

            // date options for dateToString
            let dateOptions = {
                weekday: "long",
                year: "numeric",
                month: "short",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            };

            var units = {
                temperature: "°C",
                pressure: "hPa",
                altitude: "m",
                humidity: "%"
            }

            /* add tooltip */
            var tooltip = d3.select("body")
                .append("div")
                .attr("class", "tooltip")
                .style("opacity", 0);


            /* general dimension values of graph */
            var svgWidth = +svg.attr("width");
            var svgHeight = +svg.attr("height");
            var margin = { top: 20, right: 100, bottom: 30, left: 150 };
            var width = svgWidth - margin.left - margin.right;
            var height = svgHeight - margin.top - margin.bottom;


            /* add x-axis */
            var xMin = d3.min(data, d => { return (d.timestamp); });
            var xMax = d3.max(data, d => { return d.timestamp; });
            var x = d3.scaleTime()
                .domain([d3.timeDay.offset(xMin, -1), d3.timeDay.offset(xMax, 1)])
                .range([0, width]);

            var xAxis = d3.axisBottom(x);

            /* add y-axis */
            var yMin = d3.min(data, d => { return d[currentDataKey]; });
            var yMax = d3.max(data, d => { return d[currentDataKey]; });
            var y = d3.scaleLinear()
                .domain([yMin / 1.01, yMax * 1.01])
                .range([height, 0]);

            var yAxis = d3.axisLeft(y);

            /* add brush */
            var brush = d3.brush().extent([
                    [0, 0],
                    [width, height]
                ]).on("end", brushended),
                idleTimeout,
                idleDelay = 350;


            /* create svg element with width and height values */
            var svg = d3.select("#d3-graph").append("svg")
                .attr("width", svgWidth)
                .attr("height", svgHeight)
                .append("g")
                .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

            /* Add a clipPath to prevent overflow when zoom */
            var clip = svg.append("defs").append("svg:clipPath")
                .attr("id", "clip")
                .append("svg:rect")
                .attr("width", width)
                .attr("height", height)
                .attr("x", 0)
                .attr("y", 0);

            /* Create the scatter variable: for circle */
            var scatter = svg.append("g")
                .attr("id", "scatterplot")
                .attr("clip-path", "url(#clip)");

            /* append new "g" to scatter for call brush */
            scatter.append("g")
                .attr("class", "brush")
                .call(brush);


            // add circles           
            scatter.append("g")
                .selectAll("circle")
                .data(data)
                .enter()
                .append("circle")
                .attr("cx", d => { return x(d.timestamp) })
                .attr("cy", d => { return y(d[currentDataKey]) })
                .attr("r", 5)
                .attr("fill", "#327da8")
                .on("mouseover", d => {
                    tooltip.transition()
                        .duration(500)
                        .style("opacity", .85)
                    console.log(tooltip)
                    tooltip.html("<strong>" + capitalizeFirstLetter(currentDataKey) + " : " + d[currentDataKey] + units[currentDataKey] + "<br> Timestamp : " + d.timestamp.toLocaleString("en-us", dateOptions) + "</strong> ")
                        .style("left", (d3.event.pageX) + "px")
                        .style("top", (d3.event.pageY - 30) + "px");
                })
                .on("mouseout", d => {
                    tooltip.transition()
                        .duration(300)
                        .style("opacity", 0);
                });


            // x axis    
            var xGroup = svg.append("g")
                .attr("transform", "translate(0," + height + ")")
                .attr("class", "axis axis--x")
                .call(xAxis);

            /* x-axis label */
            svg.append("text")
                .attr("class", "x-label")
                // .attr("text-anchor", "end")
                .attr("x", width +10)
                .attr("y", height +5)
                .text("Timestamp");


            // y axis    
            var yGroup = svg.append("g")
                .attr("class", "axis axis--y")
                .call(yAxis);


            /* add y-axis label */
            svg.append("text")
                .attr("class", "y-label")
                .attr("text-anchor", "end")
                .attr("y", -8)
                .attr("x", 60)
                // .attr("transform", "rotate(-90)")
                .text(capitalizeFirstLetter(currentDataKey));


            // These are needed for the brush construction to know how to scale         

            /* brush functions */
            function brushended() {
                var s = d3.event.selection;

                if (!s) {
                    if (!idleTimeout) return idleTimeout = setTimeout(idled, idleDelay);
                    x.domain(d3.extent(data, function(d) {
                        return d.timestamp;
                    })).nice();
                    y.domain(d3.extent(data, function(d) {
                        return d[currentDataKey];
                    })).nice();
                } else {
                    console.log(s[0]);
                    x.domain([s[0][0], s[1][0]].map(x.invert, x));
                    y.domain([s[1][1], s[0][1]].map(y.invert, y));
                    scatter.select(".brush").call(brush.move, null);
                }
                zoomWithBrush();
            }

            function idled() {
                idleTimeout = null;
            }

            function zoomWithBrush() {
                var t = scatter.transition().duration(750);
                svg.select(".axis--x").transition(t).call(xAxis);
                svg.select(".axis--y").transition(t).call(yAxis);
                scatter.selectAll("circle").transition(t)
                    .attr("cx", function(d) {
                        return x(d.timestamp);
                    })
                    .attr("cy", function(d) {
                        return y(d[currentDataKey]);
                    });
            }

        }

        /* add buttons according to coming data */
        function addButtons(data) {
            var buttons = document.getElementsByClassName('data--graph__button');
            for (let i = 0; i < buttons.length; i++) {
                if (data[0].hasOwnProperty(buttons[i].value)) {
                    buttonActive(buttons[i]);
                    if (!graphStarted) {
                        addGraph(buttons[i].value);
                        graphStarted = true;
                    }
                }

            }

            function buttonActive(button) {
                button.style.display = "inline";
                button.addEventListener("click", function() {
                    addGraph(button.value);
                });
            }
        }

    }


    // capitalize first letter of input
    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
});