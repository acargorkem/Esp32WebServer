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
            

            /* general dimension values of graph */
            var svgWidth = 700;
            var svgHeight = 600;
            var margin = { top: 20, right: 20, bottom: 30, left: 50 };
            var width = svgWidth - margin.left - margin.right;
            var height = svgHeight - margin.top - margin.bottom;

            /* create svg element with width and height values */
            var svg = d3.select('#d3-graph')
                .attr("width", svgWidth)
                .attr("height", svgHeight)
                .append("g")
                .attr("transform", "translate(" + margin.left + "," + margin.top + ")");


            /* add x-axis */
            var x = d3.scaleTime()
                .domain(d3.extent(data, d => { return d.timestamp; }))
                .range([0, width]);
            var xAxis = svg.append("g")
                .attr("transform", "translate(0," + height + ")")
                .attr("class", "x-axis")
                .call(d3.axisBottom(x));

            /* x-axis label */
            svg.append("text")
                .attr("class", "x-label")
                .attr("text-anchor", "end")
                .attr("x", width + 10)
                .attr("y", height - 2)
                .text("Timestamp");


            /* add y-axis */
            var y = d3.scaleLinear()
                .domain(d3.extent(data, d => {
                    return d[currentDataKey];
                }))
                .range([height, 0]);
            var yAxis = svg.append("g")
                .attr("class", "y-axis")
                .call(d3.axisLeft(y));


            /* add y-axis label */
            svg.append("text")
                .attr("class", "y-label")
                .attr("text-anchor", "end")
                .attr("y", -8)
                .attr("x", 60)
                // .attr("transform", "rotate(-90)")
                .text(capitalizeFirstLetter(currentDataKey));


            /* add tooltip */
            var tooltip = d3.select("body")
                .append("div")
                .attr("class", "tooltip")
                .style("opacity", 0)


            // Add a clipPath to prevent overflow when zoom
            var clip = svg.append("defs").append("SVG:clipPath")
                .attr("id", "clip")
                .append("SVG:rect")
                .attr("width", width)
                .attr("height", height)
                .attr("x", 0)
                .attr("y", 0);


            // Create the scatter variable: for circle and line
            var scatter = svg.append('g')
                .attr("clip-path", "url(#clip)");


            // add line
            scatter.append("path")
                .datum(data)
                .attr("fill", "none")
                .attr("stroke", "#03b6fc")
                .attr("stroke-width", 0.5)
                .attr("d", d3.line()
                    .x(d => {
                        return x(d.timestamp);
                    })
                    .y(d => {
                        return y(d[currentDataKey]);
                    }));


            
                    
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

                    tooltip.html("<strong>" + capitalizeFirstLetter(currentDataKey) + " : " + d[currentDataKey] + units[currentDataKey] + "<br> Timestamp : " + d.timestamp.toLocaleString("en-us", dateOptions) + "</strong> ")
                        .style("left", (d3.event.pageX) + "px")
                        .style("top", (d3.event.pageY - 30) + "px");
                })
                .on("mouseout", d => {
                    tooltip.transition()
                        .duration(300)
                        .style("opacity", 0);
                });


            /* add zoom attr */
            // Set the zoom and Pan features: how much you can zoom, on which part, and what to do when there is a zoom
            var zoom = d3.zoom()
                .scaleExtent([0.6, 100]) // This control how much you can unzoom (x0.5) and zoom (x100)
                .extent([
                    [0, 0],
                    [width, height]
                ])
                .on("zoom", updateChartWithZoom);

            // cover all graph with invisible area to understand zoom event
            svg.append("rect")
                .attr("width", width)
                .attr("height", height)
                .style("fill", "none")
                .style("pointer-events", "all")
                .attr('transform', 'translate(' + margin.left + ',' + margin.top + ')')
                .lower()
                .call(zoom.transform, d3.zoomIdentity.translate(10, 10).scale(0.9)) // initial zoom values
                .attr("transform", "translate(10,10) scale(.9,.9)"); // initial zoom values
            svg.call(zoom);

            // zoom function that updates graph after zoom event
            function updateChartWithZoom() {

                // recover the new scale
                var newX = d3.event.transform.rescaleX(x);
                var newY = d3.event.transform.rescaleY(y);

                // update axes with these new boundaries
                xAxis.call(d3.axisBottom(newX))
                yAxis.call(d3.axisLeft(newY))

                // update line position
                scatter
                    .selectAll("path")
                    .attr("d", d3.line()
                        .x(d => {
                            return newX(d.timestamp);
                        })
                        .y(d => {
                            return newY(d[currentDataKey]);
                        }));

                scatter
                    .selectAll("circle")
                    .attr('cx', function(d) { return newX(d.timestamp) })
                    .attr('cy', function(d) { return newY(d[currentDataKey]) });
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