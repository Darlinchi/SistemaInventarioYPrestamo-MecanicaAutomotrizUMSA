import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\ReportController::pdf
 * @see app/Http/Controllers/ReportController.php:214
 * @route '/dashboard/reports/history/pdf'
 */
export const pdf = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})

pdf.definition = {
    methods: ["get","head"],
    url: '/dashboard/reports/history/pdf',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ReportController::pdf
 * @see app/Http/Controllers/ReportController.php:214
 * @route '/dashboard/reports/history/pdf'
 */
pdf.url = (options?: RouteQueryOptions) => {
    return pdf.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\ReportController::pdf
 * @see app/Http/Controllers/ReportController.php:214
 * @route '/dashboard/reports/history/pdf'
 */
pdf.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: pdf.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\ReportController::pdf
 * @see app/Http/Controllers/ReportController.php:214
 * @route '/dashboard/reports/history/pdf'
 */
pdf.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: pdf.url(options),
    method: 'head',
})
const history = {
    pdf: Object.assign(pdf, pdf),
}

export default history