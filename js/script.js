(() => {
    "use strict";
    function isWebp() {
        function testWebP(callback) {
            let webP = new Image;
            webP.onload = webP.onerror = function() {
                callback(webP.height == 2);
            };
            webP.src = "data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA";
        }
        testWebP(function(support) {
            let className = support === true ? "webp" : "no-webp";
            document.documentElement.classList.add(className);
        });
    }
    let _slideUp = (target, duration = 500, showmore = 0) => {
        if (!target.classList.contains("_slide")) {
            target.classList.add("_slide");
            target.style.transitionProperty = "height, margin, padding";
            target.style.transitionDuration = duration + "ms";
            target.style.height = `${target.offsetHeight}px`;
            target.offsetHeight;
            target.style.overflow = "hidden";
            target.style.height = showmore ? `${showmore}px` : `0px`;
            target.style.paddingTop = 0;
            target.style.paddingBottom = 0;
            target.style.marginTop = 0;
            target.style.marginBottom = 0;
            window.setTimeout(() => {
                target.hidden = !showmore ? true : false;
                !showmore ? target.style.removeProperty("height") : null;
                target.style.removeProperty("padding-top");
                target.style.removeProperty("padding-bottom");
                target.style.removeProperty("margin-top");
                target.style.removeProperty("margin-bottom");
                !showmore ? target.style.removeProperty("overflow") : null;
                target.style.removeProperty("transition-duration");
                target.style.removeProperty("transition-property");
                target.classList.remove("_slide");
                document.dispatchEvent(new CustomEvent("slideUpDone", {
                    detail: {
                        target
                    }
                }));
            }, duration);
        }
    };
    let _slideDown = (target, duration = 500, showmore = 0) => {
        if (!target.classList.contains("_slide")) {
            target.classList.add("_slide");
            target.hidden = target.hidden ? false : null;
            showmore ? target.style.removeProperty("height") : null;
            let height = target.offsetHeight;
            target.style.overflow = "hidden";
            target.style.height = showmore ? `${showmore}px` : `0px`;
            target.style.paddingTop = 0;
            target.style.paddingBottom = 0;
            target.style.marginTop = 0;
            target.style.marginBottom = 0;
            target.offsetHeight;
            target.style.transitionProperty = "height, margin, padding";
            target.style.transitionDuration = duration + "ms";
            target.style.height = height + "px";
            target.style.removeProperty("padding-top");
            target.style.removeProperty("padding-bottom");
            target.style.removeProperty("margin-top");
            target.style.removeProperty("margin-bottom");
            window.setTimeout(() => {
                target.style.removeProperty("height");
                target.style.removeProperty("overflow");
                target.style.removeProperty("transition-duration");
                target.style.removeProperty("transition-property");
                target.classList.remove("_slide");
                document.dispatchEvent(new CustomEvent("slideDownDone", {
                    detail: {
                        target
                    }
                }));
            }, duration);
        }
    };
    let _slideToggle = (target, duration = 500) => {
        if (target.hidden) return _slideDown(target, duration); else return _slideUp(target, duration);
    };
    function spollers() {
        const spollersArray = document.querySelectorAll("[data-spollers]");
        if (spollersArray.length > 0) {
            const spollersRegular = Array.from(spollersArray).filter(function(item, index, self) {
                return !item.dataset.spollers.split(",")[0];
            });
            if (spollersRegular.length) initSpollers(spollersRegular);
            let mdQueriesArray = dataMediaQueries(spollersArray, "spollers");
            if (mdQueriesArray && mdQueriesArray.length) mdQueriesArray.forEach(mdQueriesItem => {
                mdQueriesItem.matchMedia.addEventListener("change", function() {
                    initSpollers(mdQueriesItem.itemsArray, mdQueriesItem.matchMedia);
                });
                initSpollers(mdQueriesItem.itemsArray, mdQueriesItem.matchMedia);
            });
            function initSpollers(spollersArray, matchMedia = false) {
                spollersArray.forEach(spollersBlock => {
                    spollersBlock = matchMedia ? spollersBlock.item : spollersBlock;
                    if (matchMedia.matches || !matchMedia) {
                        spollersBlock.classList.add("_spoller-init");
                        initSpollerBody(spollersBlock);
                        spollersBlock.addEventListener("click", setSpollerAction);
                    } else {
                        spollersBlock.classList.remove("_spoller-init");
                        initSpollerBody(spollersBlock, false);
                        spollersBlock.removeEventListener("click", setSpollerAction);
                    }
                });
            }
            function initSpollerBody(spollersBlock, hideSpollerBody = true) {
                let spollerTitles = spollersBlock.querySelectorAll("[data-spoller]");
                if (spollerTitles.length) {
                    spollerTitles = Array.from(spollerTitles).filter(item => item.closest("[data-spollers]") === spollersBlock);
                    spollerTitles.forEach(spollerTitle => {
                        if (hideSpollerBody) {
                            spollerTitle.removeAttribute("tabindex");
                            if (!spollerTitle.classList.contains("_spoller-active")) spollerTitle.nextElementSibling.hidden = true;
                        } else {
                            spollerTitle.setAttribute("tabindex", "-1");
                            spollerTitle.nextElementSibling.hidden = false;
                        }
                    });
                }
            }
            function setSpollerAction(e) {
                const el = e.target;
                if (el.closest("[data-spoller]")) {
                    const spollerTitle = el.closest("[data-spoller]");
                    const spollersBlock = spollerTitle.closest("[data-spollers]");
                    const oneSpoller = spollersBlock.hasAttribute("data-one-spoller");
                    const spollerSpeed = spollersBlock.dataset.spollersSpeed ? parseInt(spollersBlock.dataset.spollersSpeed) : 500;
                    if (!spollersBlock.querySelectorAll("._slide").length) {
                        if (oneSpoller && !spollerTitle.classList.contains("_spoller-active")) hideSpollersBody(spollersBlock);
                        spollerTitle.classList.toggle("_spoller-active");
                        _slideToggle(spollerTitle.nextElementSibling, spollerSpeed);
                    }
                    e.preventDefault();
                }
            }
            function hideSpollersBody(spollersBlock) {
                const spollerActiveTitle = spollersBlock.querySelector("[data-spoller]._spoller-active");
                const spollerSpeed = spollersBlock.dataset.spollersSpeed ? parseInt(spollersBlock.dataset.spollersSpeed) : 500;
                if (spollerActiveTitle && !spollersBlock.querySelectorAll("._slide").length) {
                    spollerActiveTitle.classList.remove("_spoller-active");
                    _slideUp(spollerActiveTitle.nextElementSibling, spollerSpeed);
                }
            }
            const spollersClose = document.querySelectorAll("[data-spoller-close]");
            if (spollersClose.length) document.addEventListener("click", function(e) {
                const el = e.target;
                if (!el.closest("[data-spollers]")) spollersClose.forEach(spollerClose => {
                    const spollersBlock = spollerClose.closest("[data-spollers]");
                    const spollerSpeed = spollersBlock.dataset.spollersSpeed ? parseInt(spollersBlock.dataset.spollersSpeed) : 500;
                    spollerClose.classList.remove("_spoller-active");
                    _slideUp(spollerClose.nextElementSibling, spollerSpeed);
                });
            });
        }
    }
    function uniqArray(array) {
        return array.filter(function(item, index, self) {
            return self.indexOf(item) === index;
        });
    }
    function dataMediaQueries(array, dataSetValue) {
        const media = Array.from(array).filter(function(item, index, self) {
            if (item.dataset[dataSetValue]) return item.dataset[dataSetValue].split(",")[0];
        });
        if (media.length) {
            const breakpointsArray = [];
            media.forEach(item => {
                const params = item.dataset[dataSetValue];
                const breakpoint = {};
                const paramsArray = params.split(",");
                breakpoint.value = paramsArray[0];
                breakpoint.type = paramsArray[1] ? paramsArray[1].trim() : "max";
                breakpoint.item = item;
                breakpointsArray.push(breakpoint);
            });
            let mdQueries = breakpointsArray.map(function(item) {
                return "(" + item.type + "-width: " + item.value + "px)," + item.value + "," + item.type;
            });
            mdQueries = uniqArray(mdQueries);
            const mdQueriesArray = [];
            if (mdQueries.length) {
                mdQueries.forEach(breakpoint => {
                    const paramsArray = breakpoint.split(",");
                    const mediaBreakpoint = paramsArray[1];
                    const mediaType = paramsArray[2];
                    const matchMedia = window.matchMedia(paramsArray[0]);
                    const itemsArray = breakpointsArray.filter(function(item) {
                        if (item.value === mediaBreakpoint && item.type === mediaType) return true;
                    });
                    mdQueriesArray.push({
                        itemsArray,
                        matchMedia
                    });
                });
                return mdQueriesArray;
            }
        }
    }
    let addWindowScrollEvent = false;
    setTimeout(() => {
        if (addWindowScrollEvent) {
            let windowScroll = new Event("windowScroll");
            window.addEventListener("scroll", function(e) {
                document.dispatchEvent(windowScroll);
            });
        }
    }, 0);
    document.addEventListener("DOMContentLoaded", function() {
        const customSelects = document.querySelectorAll(".calculator__select");
        customSelects.forEach(select => {
            const wrapper = select.closest(".calculator__select-wrapper");
            select.style.display = "none";
            const selectedDiv = document.createElement("div");
            selectedDiv.setAttribute("class", "select-selected");
            selectedDiv.innerHTML = select.options[select.selectedIndex].innerHTML;
            wrapper.appendChild(selectedDiv);
            const itemsDiv = document.createElement("div");
            itemsDiv.setAttribute("class", "select-items select-hide");
            for (let i = 1; i < select.length; i++) {
                const optionDiv = document.createElement("div");
                optionDiv.innerHTML = select.options[i].innerHTML;
                optionDiv.addEventListener("click", function(e) {
                    const s = this.parentNode.parentNode.querySelector("select");
                    const h = this.parentNode.previousSibling;
                    for (let i = 0; i < s.length; i++) if (s.options[i].innerHTML == this.innerHTML) {
                        s.selectedIndex = i;
                        h.innerHTML = this.innerHTML;
                        s.dispatchEvent(new Event("change"));
                        const y = this.parentNode.getElementsByClassName("same-as-selected");
                        for (let k = 0; k < y.length; k++) y[k].removeAttribute("class");
                        this.setAttribute("class", "same-as-selected");
                        break;
                    }
                    h.click();
                });
                itemsDiv.appendChild(optionDiv);
            }
            wrapper.appendChild(itemsDiv);
            selectedDiv.addEventListener("click", function(e) {
                e.stopPropagation();
                closeAllSelects(this);
                this.nextSibling.classList.toggle("select-hide");
                this.classList.toggle("select-arrow-active");
                const svgArrow = wrapper.querySelector(".calculator__select-arrow");
                if (svgArrow) svgArrow.classList.toggle("active");
            });
        });
        function closeAllSelects(elmnt) {
            const x = document.getElementsByClassName("select-items");
            const y = document.getElementsByClassName("select-selected");
            document.querySelectorAll(".calculator__select-arrow");
            const xl = x.length;
            const yl = y.length;
            const arr = [];
            for (let i = 0; i < yl; i++) if (elmnt == y[i]) arr.push(i); else {
                y[i].classList.remove("select-arrow-active");
                const wrapper = y[i].closest(".calculator__select-wrapper");
                if (wrapper) {
                    const arrow = wrapper.querySelector(".calculator__select-arrow");
                    if (arrow) arrow.classList.remove("active");
                }
            }
            for (let i = 0; i < xl; i++) if (arr.indexOf(i)) x[i].classList.add("select-hide");
        }
        document.addEventListener("click", closeAllSelects);
        const form = document.getElementById("calculatorForm");
        const resultBlock = document.getElementById("calc-result");
        if (form && resultBlock) {
            const baseRates = {
                cosmetic: 6e3,
                capital: 9500,
                full: 13e3,
                premium: 2e4
            };
            const optionAddons = {
                facade: 1500,
                roof: 2e3,
                engineering: 2500
            };
            const calcBtn = form.querySelector(".calculator__btn");
            if (calcBtn) calcBtn.addEventListener("click", function() {
                resultBlock.innerHTML = "";
            });
            form.addEventListener("submit", function(e) {
                e.preventDefault();
                const area = parseFloat(document.getElementById("area").value || "0");
                const repairType = document.getElementById("repairType").value;
                const facade = document.getElementById("facade").checked;
                const roof = document.getElementById("roof").checked;
                const engineering = document.getElementById("engineering").checked;
                if (!area || area <= 0 || !baseRates[repairType]) {
                    resultBlock.innerHTML = "";
                    resultBlock.innerHTML = '<p class="calculator__error">Укажите корректную площадь и тип ремонта.</p>';
                    return;
                }
                let rate = baseRates[repairType];
                if (facade) rate += optionAddons.facade;
                if (roof) rate += optionAddons.roof;
                if (engineering) rate += optionAddons.engineering;
                const workCost = area * rate;
                const totalCost = workCost * 2.7;
                const format = value => value.toLocaleString("ru-RU", {
                    maximumFractionDigits: 0
                });
                resultBlock.innerHTML = `\n                <h3>Ориентировочный расчёт</h3>\n                <p>Стоимость работ: <strong>${format(workCost)} ₽</strong></p>\n                <p class="calculator__result-estimate">При заказе материалов у нас ориентировочный бюджет составит <strong>${format(totalCost)} ₽</strong></p>\n                <p><small>Расчёт предварительный и не является публичной офертой. Точная стоимость рассчитывается после выезда инженера-сметчика на объект.</small></p>\n            `;
            });
        }
    });
    window["FLS"] = true;
    isWebp();
    spollers();
})();