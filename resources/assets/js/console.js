var apiEndpoints = [
    {
        'url': '/oauth/access_token',
        'method': 'POST',
        'headers': [],
        'params': ['grant_type', 'username', 'password', 'client_id', 'client_secret']
    }, {
        'url': '/api/v1/convos',
        'method': 'POST',
        'headers': ['Authorization'],
        'params': ['subject', 'recipient', 'body']
    }, {
        'url': '/api/v1/convos/{id}',
        'method': 'GET',
        'headers': ['Authorization'],
        'params': ['id']
    }, {
        'url': '/api/v1/convos',
        'method': 'GET',
        'headers': ['Authorization'],
        'params': ['limit', 'page', 'until']
    }, {
        'url': '/api/v1/convos/{id}',
        'method': 'PUT',
        'headers': ['Authorization'],
        'params': ['id', 'is_read']
    }, {
        'url': '/api/v1/convos/{id}',
        'method': 'DELETE',
        'headers': ['Authorization'],
        'params': ['id']
    }, {
        'url': '/api/v1/convos/{convoid}/messages',
        'method': 'POST',
        'headers': ['Authorization'],
        'params': ['convoid', 'body']
    }, {
        'url': '/api/v1/convos/{convoid}/messages',
        'method': 'GET',
        'headers': ['Authorization'],
        'params': ['convoid', 'limit', 'page', 'until']
    }, {
        'url': '/api/v1/convos/{convoid}/messages/{msgid}',
        'method': 'DELETE',
        'headers': ['Authorization'],
        'params': ['convoid', 'msgid']
    }
];

var CommentBox = React.createClass({

    getInitialState: function () {
        return {endpoint: null, endpointIndex: null, result: null};
    },

    handleChange: function (event) {
        if (isNaN(event.target.value)) {
            this.setState({endpoint: null});
        } else {
            var index = parseInt(event.target.value, 10);
            this.setState({
                endpoint: apiEndpoints[index],
                endpointIndex: index
            });
        }
    },

    parseResult: function (data, headers) {
        this.setState({
            result: {
                data: data,
                headers: headers
            }
        });
    },

    renderResult: function () {
        if (this.state.result) {
            return (
                <div className="panel panel-default">
                    <div className="panel-body">
                        <h4>Response header</h4>
                        <pre>{this.state.result.headers}</pre>
                        <h4>Body</h4>
                        <pre>{JSON.stringify(this.state.result.data, null, 4)}</pre>
                    </div>
                </div>
            );
        } else {
            return "";
        }
    },

    sendRequest: function () {
        var _self = this;

        var url = this.state.endpoint.url;

        var data = {};

        this.state.endpoint.params.forEach(function (param, index) {
            data[param] = _self.refs[param].getDOMNode().value;
            url = url.replace('{' + param + '}', data[param]);
        });

        var headers = {};

        this.state.endpoint.headers.forEach(function (header, index) {
            headers[header] = _self.refs[header].getDOMNode().value;
        });

        $.ajax({
            dataType: "json",
            url: url,
            method: this.state.endpoint.method,
            headers: headers,
            data: data
        }).done(function (data, textStatus, jqXHR) {
            _self.parseResult(data, jqXHR.getAllResponseHeaders());
        }).fail(function (jqXHR, textStatus, errorThrown) {
            _self.parseResult(jqXHR.responseJSON, jqXHR.getAllResponseHeaders());
        });
    },

    renderRequestForm: function () {
        if (this.state.endpoint) {

            var params = [];
            var _self = this;
            this.state.endpoint.params.forEach(function (param, index) {
                params.push(
                    <div className="form-group">
                        <label>{param}</label>
                        <input key={_self.state.endpointIndex + '_p_' + index} ref={param} type="text" className="form-control" />
                    </div>
                );
            });

            var headers = [];
            this.state.endpoint.headers.forEach(function (header, index) {
                headers.push(
                    <div className="form-group">
                        <label>{header}</label>
                        <input key={_self.state.endpointIndex + '_h_' + index} ref={header} type="text" className="form-control" />
                    </div>
                );
            });

            return (
                <div className="panel panel-default">
                    <div className="panel-body">
                        <h3>{this.state.endpoint.name}</h3>
                        { headers.length > 0 ? <h4>Headers</h4> : ""}
                        {headers}
                        { params.length > 0 ? <h4>Parameters</h4> : ""}
                        {params}
                        <button type="button" className="btn btn-default" onClick={this.sendRequest}>Send request</button>
                    </div>
                </div>
            );
        } else {
            return "";
        }
    },

    render: function () {

        var endpoints = [];
        endpoints.push(<option>Select endpoint</option>);
        apiEndpoints.forEach(function (enpoint, index) {
            endpoints.push(<option value={index}>{enpoint.method} {enpoint.url}</option>);
        });

        return (
            <div className="row">

                <form>
                    <div className="form-group">
                        <select className="form-control" onChange={this.handleChange}>
                            {endpoints}
                        </select>
                    </div>
                    {this.renderRequestForm()}
                </form>
                {this.renderResult()}
            </div>
        );
    }
});

React.render(
    <CommentBox />,
    document.getElementById('console')
);