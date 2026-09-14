// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * calculator.js
 *
 * @package   mod_statistics
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery", "core/str"], function($, Str) {
    "use strict";

    class StatisticsCalculator {
        constructor(root) {
            this.root = root;
            this.input = root.querySelector("[data-region='data-input']");
            this.error = root.querySelector("[data-region='error']");
            this.details = root.querySelector("[data-region='details']");
            this.sortedValues = root.querySelector("[data-region='sorted-values']");
            this.varianceLabel = root.querySelector("[data-region='variance-label']");
            this.standardDeviationLabel = root.querySelector("[data-region='standarddeviation-label']");
            this.numberFormatter = new Intl.NumberFormat(document.documentElement.lang || "pt-BR", {
                maximumFractionDigits: 10,
                useGrouping: true,
            });
        }

        init() {
            $(this.input).on("input", () => this.update());
            $(this.root).find("[data-region='variance-type']").on("change", () => this.update());
            $(this.root).find("[data-action='clear']").on("click", () => {
                this.input.value = "";
                this.input.focus();
                this.update();
            });
        }

        parse(raw) {
            const normalized = raw.replace(/[;\n\r\t]+/g, " ").trim();
            if (!normalized) {
                return {values: [], invalid: null};
            }

            const chunks = normalized.split(/\s+/).filter(Boolean);
            const values = [];

            for (const originalChunk of chunks) {
                const chunk = originalChunk.replace(/^[,]+|[,]+$/g, "");
                if (!chunk) {
                    continue;
                }

                if (this.isNumericToken(chunk)) {
                    values.push(this.toNumber(chunk));
                    continue;
                }

                if (chunk.includes(",")) {
                    const commaParts = chunk.split(",").filter((part) => part !== "");
                    if (commaParts.length > 1 && commaParts.every((part) => this.isNumericToken(part))) {
                        commaParts.forEach((part) => values.push(this.toNumber(part)));
                        continue;
                    }
                }

                return {values: [], invalid: originalChunk};
            }

            return {values, invalid: null};
        }

        isNumericToken(token) {
            return /^[+-]?(?:\d+(?:[.,]\d+)?|[.,]\d+)(?:[eE][+-]?\d+)?$/.test(token);
        }

        toNumber(token) {
            return Number(token.replace(",", "."));
        }

        getVarianceType() {
            const selected = this.root.querySelector("[data-region='variance-type']:checked");
            return selected ? selected.value : "population";
        }

        calculate(values, varianceType) {
            const sorted = [...values].sort((a, b) => a - b);
            const count = sorted.length;
            const sum = sorted.reduce((total, value) => total + value, 0);
            const mean = sum / count;
            const midpoint = Math.floor(count / 2);
            const median = count % 2 === 0 ? (sorted[midpoint - 1] + sorted[midpoint]) / 2 : sorted[midpoint];
            const minimum = sorted[0];
            const maximum = sorted[count - 1];
            const range = maximum - minimum;
            const divisor = varianceType === "sample" ? count - 1 : count;
            const squaredDifferenceSum = sorted.reduce((total, value) => total + Math.pow(value - mean, 2), 0);
            const variance = divisor > 0 ? Math.max(0, squaredDifferenceSum / divisor) : null;
            const standardDeviation = variance === null ? null : Math.sqrt(variance);

            const frequencies = new Map();
            sorted.forEach((value) => {
                const key = String(value);
                frequencies.set(key, (frequencies.get(key) || 0) + 1);
            });
            const highestFrequency = Math.max(...frequencies.values());
            const modes = highestFrequency <= 1 ? [] : [...frequencies.entries()]
                .filter(([, frequency]) => frequency === highestFrequency)
                .map(([value]) => Number(value));

            return {
                count,
                sum,
                mean,
                median,
                modes,
                minimum,
                maximum,
                range,
                variance,
                standardDeviation,
                sorted,
            };
        }

        async update() {
            const parsed = this.parse(this.input.value);
            this.hideError();

            if (parsed.invalid !== null) {
                const message = await Str.get_string("invalidvalue", "mod_statistics");
                this.showError(message + " (" + parsed.invalid + ")");
                this.resetResults();
                return;
            }

            if (parsed.values.length === 0) {
                this.resetResults();
                return;
            }

            const varianceType = this.getVarianceType();
            const result = this.calculate(parsed.values, varianceType);
            const typeLabel = await Str.get_string(varianceType, "mod_statistics");
            this.varianceLabel.textContent = typeLabel;
            this.standardDeviationLabel.textContent = typeLabel;

            this.setResult("count", String(result.count));
            this.setResult("sum", this.formatNumber(result.sum));
            this.setResult("mean", this.formatNumber(result.mean));
            this.setResult("median", this.formatNumber(result.median));
            this.setResult("minimum", this.formatNumber(result.minimum));
            this.setResult("maximum", this.formatNumber(result.maximum));
            this.setResult("range", this.formatNumber(result.range));

            const modeText = result.modes.length === 0
                ? await Str.get_string("nomode", "mod_statistics")
                : result.modes.map((value) => this.formatNumber(value)).join("; ");
            this.setResult("mode", modeText);

            if (result.variance === null) {
                const message = await Str.get_string("needtwovalues", "mod_statistics");
                this.setResult("variance", "-");
                this.setResult("standarddeviation", "-");
                this.showError(message);
            } else {
                this.setResult("variance", this.formatNumber(result.variance));
                this.setResult("standarddeviation", this.formatNumber(result.standardDeviation));
            }

            this.sortedValues.textContent = result.sorted.map((value) => this.formatNumber(value)).join(" · ");
            this.details.classList.remove("d-none");
        }

        setResult(name, value) {
            const element = this.root.querySelector("[data-result='" + name + "']");
            if (element) {
                element.textContent = value;
            }
        }

        resetResults() {
            this.root.querySelectorAll("[data-result]").forEach((element) => {
                element.textContent = "-";
            });
            this.details.classList.add("d-none");
            this.sortedValues.textContent = "";
        }

        formatNumber(value) {
            if (!Number.isFinite(value)) {
                return "-";
            }
            const normalized = Math.abs(value) < 1e-12 ? 0 : value;
            return this.numberFormatter.format(normalized);
        }

        showError(message) {
            this.error.textContent = message;
            this.error.classList.remove("d-none");
        }

        hideError() {
            this.error.textContent = "";
            this.error.classList.add("d-none");
        }
    }

    return {
        init: function() {
            const root = document.querySelector("[data-region='statistics-calculator']");
            if (!root) {
                return;
            }
            const calculator = new StatisticsCalculator(root);
            calculator.init();
        },
    };
});
