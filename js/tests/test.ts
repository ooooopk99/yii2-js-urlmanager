import test from 'ava';
import UrlManager from '../src/UrlManager';

let urlManager = new UrlManager({
    enablePrettyUrl: true,
    rules: [
        {
            name: '/',
            route: '/site/index'
        },
        {
            name: '/foo/<id:(\\d+)>/bar/<type:(first|second)>',
            route: '/foo/bar'
        }
    ]
});

test('it should create correct url which is contained parameters', t => {
    t.is('/foo/10/bar/first', urlManager.createUrl('/foo/bar', {
        id : 10,
        type : "first"
    }));
});

test('it should create correct url without parameters', t => {
    t.is('/', urlManager.createUrl('/site/index'));
});

test('it should create correct url with query string if passed parameters aren\'t contained in name', t => {
    t.is('/?foo=bar&param2=value2', urlManager.createUrl('/site/index', {
        'foo' : 'bar',
        'param2' : 'value2'
    }));
});

test('it should create url if route doesn\'t found', t => {
    t.is('/undefined-url/foo/bar', urlManager.createUrl('/undefined-url/foo/bar'));
});

test('it should create url with query string if route doesn\'t found and parameters passed', t => {
    t.is('/undefined-url/foo/bar?param1=value1&param2=value2', urlManager.createUrl('/undefined-url/foo/bar', {
        'param1' : 'value1',
        'param2' : 'value2'
    }));
});