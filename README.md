# Smart-Mirror-Display; Backend

## URL to parse

1. get bus-station information around user

```text
https://map.naver.com/v5/api/search?pcweb&query=%EB%B2%84%EC%8A%A4%EC%A0%95%EB%A5%98%EC%9E%A5&type=bus-station&searchCoord=${Longtitude};${Lattitude}
```

1. Get Bus ID

```text
https://map.naver.com/v5/api/bus/arrival?lang=ko&stationId=91042&caller=pc_map
```

1. Get Bus Remaining Time

```text
https://map.naver.com/v5/api/bus/arrival?lang=ko&stationId=91042&caller=pc_map
```
